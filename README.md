pouyasoft_ir/simple-date-bundle
========================
**A bundle for persian date in Symfony2**

Install
--------------
- **Install via Composer:**

```
$ php composer require pouyasoft_ir/simple-date-bundle
```

Service
--------------
**Service Name:** pouya_soft.j_sdate_service

**Functions:**
* **georgianToPersian:**  
    Convert Georgian calendar (DateTime) To Persian (String).  
    ***Parameters:***  
    * georgian: DateTime (default: `null`)
    * format: string (default: `yyyy/MM/dd`) [View Intl Format](http://userguide.icu-project.org/formatparse/datetime)
    * locale: string (default: `fa`) (e.g. fa, fa_IR, en, en_US, en_UK, ...)
    * calendar: string (default: `persian`) (e.g. gregorian, persian, islamic, ...)
    * latinizeDigit: bool (default: `false`) Convert Persian numbers to Latin Numbers.
* **persianToGeorgian:**  
    Convert Persian calendar (String) To Georgian (DateTime).  
    ***Parameters:***  
    * persian: string
    * format: string (default: `yyyy/MM/dd`) [View Intl Format](http://userguide.icu-project.org/formatparse/datetime)
    * locale: string (default: `fa`) (e.g. fa, fa_IR, en, en_US, en_UK, ...)
    * calendar: string (default: `persian`) (e.g. gregorian, persian, islamic, ...)
* **intlDateTimeInstance:**  
    Return new Instance of IntlDateTime. [Visit Blog of Ali Farhadi](http://farhadi.ir/blog/1389/02/10/persian-calendar-for-php-53/)

**Sample:**
```php
$shamsiString = $this->get('pouya_soft.j_sdate_service')->georgianToPersian(new \DateTime(), 'yyyy-MM-dd E');
//result: ۱۳۹۴-۱۱-۲۲ دوشنبه
$shamsiString = $this->get('pouya_soft.j_sdate_service')->persianToGeorgian('1394-11-22 دوشنبه', 'yyyy-MM-dd E');
//result: An instance of DateTime
```

Twig
--------------
**Functions:**  
* **gpDate:**  
    Convert Georgian calendar (DateTime) To Persian (String).  
    ***Parameters:***  
    * georgian: DateTime (default: `null`)
    * format: string (default: `yyyy/MM/dd`) [View Intl Format](http://userguide.icu-project.org/formatparse/datetime)
    * locale: string (default: `fa`) (e.g. fa, fa_IR, en, en_US, en_UK, ...)
    * calendar: string (default: `persian`) (e.g. gregorian, persian, islamic, ...)
    * latinizeDigit: bool (default: `false`) Convert Persian numbers to Latin Numbers.
* **pgDate:**  
    Convert Persian calendar (String) To Georgian (DateTime).  
    ***Parameters:***  
    * persian: string
    * format: string (default: `yyyy/MM/dd`) [View Intl Format](http://userguide.icu-project.org/formatparse/datetime)
    * locale: string (default: `fa`) (e.g. fa, fa_IR, en, en_US, en_UK, ...)
    * calendar: string (default: `persian`) (e.g. gregorian, persian, islamic, ...)

**Sample:**
```twig
{{ date|gpDate }} <br>
{{ date|gpDate('yyyy-MM-dd E') }} <br>
{{ '1394/11/22'|gpDate }} <br>
{{ '1394-11-22 دوشنبه'|gpDate('yyyy-MM-dd E') }} <br>
```

Form
--------------
**Type Name:** PouyaSoftSDateType  

**Parameters:**
* serverFormat: string (default: `yyyy/MM/dd`) [View Intl Format](http://userguide.icu-project.org/formatparse/datetime)  
* clientFormat: string (default: `yy/m/d`) [View DatePicker Format](https://api.jqueryui.com/datepicker/#utility-formatDate)  
* attr: array  
    You can add other DatePicker options to this param, but must change uppercase letters to lower and add dash before it. (see Samples)
     
***note:*** Result of serverFormat and clientFormat, must be the same.

**Sample:**
```php
$builder
    ->add('date', PouyaSoftSDateType::class, [
        'serverFormat' => 'yyyy/MM/dd',
        'pickerOptions' => [
            'Format' => 'yyyy/MM/dd',
            'EnableTimePicker' => true,
            'GroupId' => 'group1',
            'FromDate' => true,
            'DisableBeforeToday' => true,
        ]
    ])
    ->add('date2', PouyaSoftSDateType::class, [
        'serverFormat' => 'yyyy-MM-dd E',
        'pickerOptions' => [
            'Format' => 'yyyy/MM/dd',
            'EnableTimePicker' => true,
            'GroupId' => 'group1',
            'ToDate' => true,
        ]
    ])
```


Date Picker
--------------
**Requirements:**
* Bootstrap
* Jquery

**Add this lines to head tag in `base.html.twig` file:**

```html
<head>
    ...
	
    <link rel="stylesheet" href="{{ asset('bundles/pouyasoftsdate/MdBootstrapPersianDateTimePicker/jquery.md.bootstrap.datetimepicker.style.css') }}" />
    ...
</head>
```

**Add this lines to end of body tag in `base.html.twig` file:**
```html
<script type="text/javascript" src="{{ asset('bundles/pouyasoftsdate/MdBootstrapPersianDateTimePicker/jquery.md.bootstrap.datetimepicker.js') }}"></script>
```

**Add this lines to `config/packages/twig.yaml` file:**
```yaml
twig:
    form_themes:
        - '@PouyaSoftSDate/form/form_s_date.html.twig'
```

**References:**
* [Blog of Ali Farhadi](http://farhadi.ir/blog/1389/02/10/persian-calendar-for-php-53/)
* [View Intl Format](http://userguide.icu-project.org/formatparse/datetime)
* [Class Intldateformatter](http://php.net/manual/en/class.intldateformatter.php)
* [MD.BootstrapPersianDateTimePicker](https://github.com/Mds92/MD.BootstrapPersianDateTimePicker)
