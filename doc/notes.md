# DUF v2.0 - Příklady a poznámky


## Renderování

 - Obecný kód, který prochází konfiguraci formuláře a kreslí, je příliš pomalý. Je potřeba konfiguraci zkompilovat do PHP třídy a tu pak jen volat. Podobně, jako to dělá [Twig](http://twig.sensiolabs.org/doc/internals.html).

## Základní formuláře

### Řádkový formulář

 - Tradiční řádkový layout bez tabulky.

 - Automaticky generovatelný z modelu.

### Tabulkový formulář

 - Tradiční tabulkový layout. Levý sloupec obsahuje popisky, pravý pak políčka formuláře. Výjimkou můžou být checkboxy.

 - Automaticky generovatelný z modelu.

### Řádkový pohled

 - Read-only varianta Řádkového formuláře. Hodnoty jsou zobrazeny na samostatných řádcích.

 - Pokud je popisek volitelný, je to pro mnoho jednoduchých entit dostačující pohled. Například novinky nebo zápisky v blogu, kde je jen titulek, autor, datum zveřejnění a text.

### Tabulkový pohled

 - Read-only varianta Tabulkového formuláře.

 - Pohled hodící se jako výchozí do administrace, kde nejsou nároky na vzhled, ale je potřeba entitu vidět hned a bez práce.

## Jednoduché kolekce

### Seznam

 - Prostý seznam instancí entit. Layout neříká nic o vykreslení jednotlivých entit.

 - Pro vykreslení entit se hodí vložit Řádkový pohled, nebo jakýkoliv jiný.

### Tabulka

 - Tabulka, kde každý řádek odpovídá jedné instanci entity a každý sloupec jedné vlastnosti entity.

 - Obsah buněk tabulky je kreslen pomocí běžného políčka v read-only režimu (typicky `<span>` nebo `<a>`).

 - Tabulka může reprezentovat strom načtený jako seznam. Každý řádek má dané odsazení, jinak to je jen seznam.

 - Automaticky generovatelný z modelu.

### Strom ze seznamu

 - Vykreslení stromu, který je z databáze načten jako seznam (např. nested sets; každý řádek zná svou hloubku ve stromu).

 - Vhodný pro seznamy kategorií nebo menu.

### Opravdový strom

 - Vykreslení stromu, který je načten jako skutečná rekurzivní struktura, např. z JSON souboru.

 - Výstup je stejný jako v případě Stromu ze seznamu, liší se algoritmem procházení datové struktury.

## Stránkování a filtrování

 - Filtrování má stávající DUF relativně dobře řešené (i když má pár nedostatků).

 - Filtry jsou key-value struktura předávaná v query parametrech URL (za otazníkem). Načítání kolekce tyto parametry interpretuje a DUF obsahuje widgety pro jejich generování. Je tak řešeno i stránkování.

 - Viz „[faceted search](https://scholar.google.cz/scholar?q=faceted+search)“ a [FlupdoGenericListing v libSmalldb](https://git.frozen-doe.net/cascade/libsmalldb/blob/master/class/FlupdoGenericListing.php).

## Dynamické kolekce

 - Seznam či tabulka editovatelných instancí entit, do kterého je možné přidávat další instance.

 - Tlačítka na přidávání a ubírání i bez Javascriptu.

## Kolekce o pevné struktuře

### Kalendář

 - Denní, vícedenní sloupcový, týdenní mřížka (7 buněk bez hodin), malý měsíční a velký měsíční pohled.

 - Entity jsou vykresleny do předem dané struktury -- kalendáře.

 - Je potřeba mapovat vlastnosti entit na vlastnosti kalendáře (styl políček a podobně).

### Plán

 - Tabulka, kde řádky odpovídají instancím jedné entity, sloupce časovému údaji nebo instancím jiné entity a obsah buněk M:N vazbě mezi nimi.

 - Stejně jako u Kalendáře je potřeba mapovat vlastnosti entitu na vlastnosti tabulky.

### Grafy, mapy

 - Integrace s 3rd-party knihovnami na kreslení map a grafů.

 - Lze jim elegantně předat data a umožnit tak integraci s DUF?

## Seskupené kolekce

### Seznam seskupený podle vlastnosti

 - Prostý seznam instancí jedné entity, avšak tyto jsou seskupeny podle zvolené vlastnosti této entity. Vstupem je jeden seznam. Nadpis skupiny je tvořen z vlastností první instance ve skupině.

 - Například seznam událostí seskupený podle měsíců.

### Seznam seskupený podle vazby na jinou entitu

 - Prostý seznam instancí jedné entity, avšak tyto jsou seskupeny podle vazby na jinou entitu. Vstupem je seznam a mapa -- seznam na iterování, mapa na vykreslení nadpisů skupin.

## Kombinované kolekce

 - Kolekce procházené podle zcela nepředpokládatelných a velmi šílených pravidel. Typicky obsah jedné kolekce řídí styl procházení ostatních kolekcí, někdy i jejich obsah.

 - Příklad: Seznam katalogů, který má u každého katalogu vyjmenované jeho kategorie a u každé kategorie několik nejprodávanějších produktů a příznak, zda je uživatel přihlášen k odběru novinek v dané kategorii nebo i celém katalogu.

## Příliš komplikovaný layout

 - Jak umožnit implementaci vlastního layoutu?

 - Framework se musí umět uhnout z cesty, pokud je daný úkol nad jeho síly. Ale pořád by měly být nástroje frameworku snadno dostupné, aby programátor nemusel dělat vše znovu.

## Uživatelská políčka

 - Jak umožnit snadnou tvorbu vlastních políček?

 - Renderování, validace, konverze dat mezi reprezentací ve formuláři a v datech vrácených formulářem?

## Integrace s React a Angular

 - Jak je realizovatelná kooperace s knihovnami React a Angular?

 - Možnost nakreslit si šablonu pro tyto knihovny a současně ji použít pro vykreslení statických stránek?