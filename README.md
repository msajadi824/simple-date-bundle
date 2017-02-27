pouyasoft_ir/simple-date-bundle
========================
**A bundle for persian date in Symfony2**

Install
--------------
- **Install via Composer:**

```
$ php composer require pouyasoft_ir/simple-date-bundle
```

- **Add to AppKernel:**

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new PouyaSoft\SDateBundle\PouyaSoftSDateBundle(),
    }
}
```

- **install assets:** 

```
$ php app/console assets:install
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
    * latinizeDigit: bool (default: `true`) Convert Persian numbers to Latin Numbers.
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
//result: 1394-11-22 دوشنبه
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
    * latinizeDigit: bool (default: `true`) Convert Persian numbers to Latin Numbers.
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

**Sample:**
```php
$builder
    ->add('date', PouyaSoftSDateType::class, [
        'serverFormat' => 'yyyy/MM/dd',
        'clientFormat' => 'yy/m/d',
        'attr' => [
            'data-min-date' => '-100y',
            'data-max-date' => '-1y',
            'data-year-range' => 'c-100:c+100',
            'data-default-date' => '-20y'
        ]
    ])
    ->add('date2', PouyaSoftSDateType::class, [
        'serverFormat' => 'yyyy-MM-dd E',
        'clientFormat' => 'yy-m-d DD',
        'attr' => [
            'data-min-date' => '0',
            'data-max-date' => '+100',
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
    <link rel="stylesheet" href="{{ asset('bundles/pouyasoftsdate/lib/bootstrap-datepicker/bootstrap-datepicker.min.css') }}"/>
    ...
</head>
```

**Add this lines to end of body tag in `base.html.twig` file:**
```html
<script type="text/javascript" src="{{ asset('bundles/pouyasoftsdate/lib/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('bundles/pouyasoftsdate/lib/bootstrap-datepicker/bootstrap-datepicker.fa.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('bundles/pouyasoftsdate/jsDatePicker.js') }}"></script>
```

**Add this lines to `app/config.yml` file:**
```yaml
twig:
    form_themes:
        - 'PouyaSoftSDateBundle:Form:form_s_date.html.twig'
```

**References:**
* [Blog of Ali Farhadi](http://farhadi.ir/blog/1389/02/10/persian-calendar-for-php-53/)
* [View Intl Format](http://userguide.icu-project.org/formatparse/datetime)
* [Class Intldateformatter](http://php.net/manual/en/class.intldateformatter.php)
* [Bootstrap Jalali Datepicker](http://mousavian.github.io/bootstrap-jalali-datepicker/)
* [JqueryUI DatePicker](https://api.jqueryui.com/datepicker)