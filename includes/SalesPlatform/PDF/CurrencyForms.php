<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

$sp_numeric_forms = array (
    'ru_ru' => array(
        '0' => 'ноль',
        '10^3' => array('тысяча', 'тысячи', 'тысяч', 1),
        '10^6' => array('миллион', 'миллиона', 'миллионов',  0),
        '10^9' => array('миллиард', 'миллиарда', 'миллиардов',  0),
        '10^12' => array('триллион', 'триллиона', 'триллионов',  0),
        '100' => array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот', 'восемьсот','девятьсот'),
        '11' => array('','десять','одиннадцать','двенадцать','тринадцать', 'четырнадцать','пятнадцать','шестнадцать','семнадцать', 'восемнадцать','девятнадцать','двадцать'),
        '10' => array('','десять','двадцать','тридцать','сорок','пятьдесят', 'шестьдесят','семьдесят','восемьдесят','девяносто'),
        '1' => array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),// m
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять') // f
        ),
    ),

    'en_us' => array(
        '0' => 'zero',
        '10^3' =>  array('thousand', 'thounsands', 'thousands', 0),
        '10^6' => array('million', 'millions', 'millions',  0),
        '10^9' => array('billion', 'billions', 'billions',  0),
        '10^12' => array('trillion', 'trillions', 'trillions',  0),
        '100' => array('','one hundred','two hundreds','three hundreds','four hundreds','five hundreds','six hundreds', 'seven hundreds', 'eight hundreds','nine hundreds'),
        '11' => array('','ten','eleven','twelve','thirteen', 'fourteen','fifteen','sixteen','seventeen', 'eighteen','nineteen','twenty'),
        '10' => array('','ten','twenty','thirty','fourty','fifty', 'sixty','seventy','eighty','ninety'),
        '1' => array(
            array('','one','two','three','four','five','six','seven', 'eight','nine'),// m
            array('','one','two','three','four','five','six','seven', 'eight','nine') // f
        )
    ),

    'ua_ua' => array(
        '0' => 'нуль',
        '10^3' => array('тисяча', 'тисячi', 'тисяч', 1),
        '10^6' => array('мiлiон', 'мiлiона', 'мiлiонов',  0),
        '10^9' => array('мiльярд', 'мiльярда', 'мiльярдов',  0),
        '10^12' => array('трильйон', 'трильйона', 'трильйонов',  0),
        '100' => array('','сто','двiстi','триста','чотириста','п\'ятсот','шiстсот', 'сiмсот', 'вiсiмсот','дев\'ятсот'),
        '11' => array('','десять','одинадцять','дванадцять','тринадцять', 'чотирнадцять','п\'ятнадцять','шiстнадцять','сiмнадцять', 'вiсiмнадцять','дев\'ятнадцять','двадцять'),
        '10' => array('','десять','двадцять','тридцять','сорок','п\'ятдесят', 'шiстдесят','сiмдесят','вiсiмдесят','дев\'яносто'),
        '1' => array(
            array('','один','два','три','чотири','п\'ять','шiсть','сiм', 'вiсiм','дев\'ять'),// m
            array('','одна','двi','три','чотири','п\'ять','шiсть','сiм', 'вiсiм','дев\'ять') // f
        ),
    ),
    
);


$sp_currency_forms = array (

    'ru_ru' => array(
        'RUB' => array(array('рубль', 'рубля', 'рублей',  0),
                       array('копейка', 'копейки', 'копеек', 1)),
        'UAH' => array(array('гривна', 'гривны', 'гривен',  0),
                       array('копейка', 'копейки', 'копеек', 1)),
        'USD' => array(array('доллар', 'доллара', 'долларов',  0),
                       array('цент', 'цента', 'центов', 0)),
        'EUR' => array(array('евро', 'евро', 'евро',  0),
                       array('цент', 'цента', 'центов', 0)),
    ),

    'en_us' => array(
        'RUB' => array(array('ruble', 'rubles', 'rubles',  0),
                       array('copeck', 'copecks', 'copecks', 0)),
        'UAH' => array(array('hryvna', 'hryvnas', 'hryvnas',  0),
                       array('copeck', 'copecks', 'copecks', 1)),
        'USD' => array(array('dollar', 'dollars', 'dollars',  0),
                       array('cent', 'cents', 'cents', 0)),
        'EUR' => array(array('euro', 'euro', 'euro',  0),
                       array('cent', 'cents', 'cents', 0)),
    ),

    'ua_ua' => array(
        'RUB' => array(array('рубль', 'рубля', 'рублів',  0),
                       array('копiйка', 'копiйки', 'копiйок', 1)),
        'UAH' => array(array('гривня', 'гривни', 'гривень',  0),
                       array('копiйка', 'копiйки', 'копiйок', 1)),
        'USD' => array(array('долар', 'долара', 'доларів',  0),
                       array('цент', 'цента', 'центів', 0)),
        'EUR' => array(array('євро', 'євро', 'євро',  0),
                       array('цент', 'цента', 'центів', 0)),
    ),

);

$sp_date_forms = array (

    'ru_ru' => array('Января',
                     'Февраля',
                     'Марта',
                     'Апреля',
                     'Мая',
                     'Июня',
                     'Июля',
                     'Августа',
                     'Сентября',
                     'Октября',
                     'Ноября',
                     'Декабря'),

    'en_us' => array('January',
                     'February',
                     'March',
                     'April',
                     'May',
                     'June',
                     'July',
                     'August',
                     'September',
                     'October',
                     'November',
                     'December'),

    'ua_ua' => array('Сiчня',
                     'Лютого',
                     'Березня',
                     'Квiтня',
                     'Травня',
                     'Червня',
                     'Липня',
                     'Серпня',
                     'Вересня',
                     'Жовтня',
                     'Листопада',
                     'Грудня'),

);

?>
