<?php
/*
 * Copyright (c) 2011-2015, Josef Kufner  <josef@kufner.cz>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Duf;

/**
 * Generate a thumbnail of given size, always preserves aspect ratio.
 */
class Thumbnail
{

	/**
	 * Generate thumbnail from file `$filename` and store it into file `$target_file`.
	 *
	 * Tip: use X-SendFile header to instruct HTTP server to pass file to client.
	 *
	 * @param $target_file Thumbnail file name.
	 * @param $filename File name of source image.
	 * @param $width Thumbnail width in pixels.
	 * @param $height Thumbnail height in pixels.
	 * @param $resize_mode How image should be fit into thumbnail:
	 * 	'fit' (add missing margin), 'fill' (crop redundant margin),
	 * 	'same_height' (variable thumbnail width, fixed height).
	 */
	public static function generateThumbnail($target_file, $filename, $width, $height, $resize_mode)
	{
		// Content type
		//header('Content-Type: image/jpeg');

		// Get new dimensions
		$size_orig = getimagesize($filename);
		if ($size_orig === false) {
			return false;
		}
		list($w_orig, $h_orig, $type) = $size_orig;

		$exif = @exif_read_data($filename);
		$ort = @$exif['Orientation'];

		list($w_dst, $h_dst, $w_src, $h_src, $x_src, $y_src, $needs_rotation, $needs_flip)
			= static::calculateTransformParameters($w_orig, $h_orig, $ort, $resize_mode, $width, $height);

		// Load source image
		$image = imagecreatefromstring(file_get_contents($filename));
		if ($image === false) {
			return false;
		}

		// Prepare thumbnail image
		$image_p = imagecreatetruecolor($w_dst, $h_dst);
		if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
			$transparent_color = imagecolorallocatealpha($image_p, 32, 32, 32, 0);	// no transparency, use #888; FIXME: configurable background color
			imagecolortransparent($image_p, $transparent_color);
			imagefill($image_p, 0, 0, $transparent_color);
			imagealphablending($image_p, true);
			imagesavealpha($image_p, true);
		}

		// Resample
		imagecopyresampled($image_p, $image, 0, 0, $x_src, $y_src, $w_dst, $h_dst, $w_src, $h_src);

		// Flip if required
		if ($needs_flip) {
			//$image_p = imageflip($image_p, $needs_flip);	// FIXME
		}

		// Rotate if required
		if ($needs_rotation) {
			$image_p = imagerotate($image_p, $needs_rotation, 0);
		}

		// Result
		return imagejpeg($image_p, $target_file, 92);
	}


	public static function calculateThumbnailSize($w_orig, $h_orig, $ort, $resize_mode, $w_dst, $h_dst)
	{
		list($w_dst, $h_dst, $w_src, $h_src, $x_src, $y_src, $needs_rotation, $needs_flip)
			= static::calculateTransformParameters($w_orig, $h_orig, $ort, $resize_mode, $w_dst, $h_dst);

		if ($needs_rotation == 90 || $needs_rotation == 270) {
			return array($h_dst, $w_dst);
		} else {
			return array($w_dst, $h_dst);
		}
	}


	private static function calculateTransformParameters($w_orig, $h_orig, $ort, $resize_mode, $w_dst, $h_dst)
	{
		//debug_dump(get_defined_vars());
		$x_src = 0;
		$y_src = 0;
		$w_src = $w_orig;
		$h_src = $h_orig;
		$ratio_orig = $w_orig / $h_orig;
		$ratio_dst  = $w_dst / $h_dst;

		// If rotate is required, swap w_dst and h_dst
		switch($ort) {

			// No rotation
			case 0: // missing info
			case 1: // nothing
			case 2: // horizontal flip
			case 3: // 180 rotate left
			case 4: // vertical flip
				switch ($resize_mode) {

					default:
					case 'fit':
						if ($ratio_orig > $ratio_dst) {
							// original is wider -- reduce height
							if ($w_dst > $w_orig) {
								$w_dst = $w_orig;	// Do not enlarge image
							}
							$h_dst = $w_dst / $ratio_orig;
						} else {
							// original is taller -- reduce width
							if ($h_dst > $h_orig) {
								$h_dst = $h_orig;	// Do not enlarge image
							}
							$w_dst = $h_dst * $ratio_orig;
						}
						break;

					case 'fill':
						if ($ratio_orig > $ratio_dst) {
							// original is wider -- crop sides
							$w_src = $h_src * $ratio_dst;
							$x_src = ($w_orig - $w_src) / 2;
						} else {
							// original is taller -- crop top & bottom
							$h_src = $w_src / $ratio_dst;
							$y_src = ($h_orig - $h_src) / 2;
						}
						break;

					case 'same_height':
						// height is given -- calculate width
						$w_dst = $h_dst * $ratio_orig;
						break;
				}
				break;

			// With rotation
			case 5: // vertical flip + 90 rotate right
			case 6: // 90 rotate right
			case 7: // horizontal flip + 90 rotate right
			case 8: // 90 rotate left
				switch ($resize_mode) {

					default:
					case 'fit':
						if ($ratio_orig > $ratio_dst) {
							// original is wider -- reduce height
							$h_dst = $w_dst;
							$w_dst = $h_dst * $ratio_orig;
						} else {
							// original is taller -- reduce width
							$w_dst = $h_dst;
							$h_dst = $w_dst / $ratio_orig;
						}
						break;

					case 'fill':
						if ($ratio_orig > $ratio_dst) {
							// original is wider -- crop sides
							$w_src = $h_src;
							$h_src = $w_src * $ratio_dst;
							$x_src = ($w_orig - $w_src) / 2;
						} else {
							// original is taller -- crop top & bottom
							$h_src = $w_src;
							$w_src = $h_src / $ratio_dst;
							$y_src = ($h_orig - $h_src) / 2;
						}
						$x = $h_dst;
						$h_dst = $w_dst;
						$w_dst = $x;
						break;

					case 'same_height':
						// height is given -- calculate width
						$w_dst = $h_dst;
						$h_dst = $w_dst / $ratio_orig;
						break;
				}
				break;
		}

		// Rotation & flip
		switch($ort)
		{
			default:
			case 1: // nothing
				$needs_rotation = 0;
				$needs_flip     = null;
				break;

			case 2: // horizontal flip
				$needs_rotation = 0;
				$needs_flip     = 'h';
				break;

			case 3: // 180 rotate left
				$needs_rotation = 180;
				$needs_flip     = null;
				break;

			case 4: // vertical flip
				$needs_rotation = 0;
				$needs_flip     = 'v';
				break;

			case 5: // vertical flip + 90 rotate right
				$needs_rotation = 270;
				$needs_flip     = 'v';
				break;

			case 6: // 90 rotate right
				$needs_rotation = 270;
				$needs_flip     = null;
				break;

			case 7: // horizontal flip + 90 rotate right
				$needs_rotation = 270;
				$needs_flip     = 'h';
				break;

			case 8: // 90 rotate left
				$needs_rotation = 90;
				$needs_flip     = null;
				break;
		}

		return array($w_dst, $h_dst, $w_src, $h_src, $x_src, $y_src, $needs_rotation, $needs_flip);
	}

};


