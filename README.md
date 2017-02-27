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
    ***Parameter:***  
    1. georgian: DateTime (default: `null`)
    2. format: string (default: `yyyy/MM/dd`) [View Intl Format](http://userguide.icu-project.org/formatparse/datetime)
    3. locale: string (default: `fa`) (e.g. fa, fa_IR, en, en_US, en_UK, ...)
    4. calendar: string (default: `persian`) (e.g. gregorian, persian, islamic, ...)
    5. latinizeDigit: bool (default: `true`)
* **persianToGeorgian:**  
    Convert Persian calendar (String) To Georgian (DateTime).  
    ***Parameter:***  
    1. persian: string
    2. format: string (default: `yyyy/MM/dd`) [View Intl Format](http://userguide.icu-project.org/formatparse/datetime)
    3. locale: string (default: `fa`) (e.g. fa, fa_IR, en, en_US, en_UK, ...)
    4. calendar: string (default: `persian`) (e.g. gregorian, persian, islamic, ...)
* **intlDateTimeInstance:**  
    Return new Instance of IntlDateTime. [Visit Blog of Ali Farhadi](http://farhadi.ir/blog/1389/02/10/persian-calendar-for-php-53/)

**Example:**
```php
$shamsiString = $this->get('pouya_soft.j_sdate_service')->georgianToPersian(new \DateTime(), 'yyyy-MM-dd E');
//result: 1394-11-22 دوشنبه
$shamsiString = $this->get('pouya_soft.j_sdate_service')->persianToGeorgian('1394-11-22 دوشنبه', 'yyyy-MM-dd E');
//result: An instance of DateTime
```

Twig
--------------
**Functions:**
- **jSDate:** 
Convert Miladi (DateTime) To Shamsi (String).  (Parameter: separator - default: /)

**Example:**
```twig
{{ date|jSDate }} <br>
{{ date|jSDate('-') }} <br>
```

Form
--------------
**Type Name:** jSDate (Parameter: separator - default: /)

**Example:**
```php
$builder
    ->add('date', 'jSDate', ['separator' => '/'])
```


Date Picker
--------------
**Requirements:**
- Bootstrap
- Jquery

**Add this to head tag in 'base.html.twig' file:**

```html
<head>
    ...
    <link rel="stylesheet" href="{{ asset('bundles/pouyasoftsdate/lib/bootstrap-datepicker/bootstrap-datepicker.min.css') }}"/>
    ...
</head>
```

**Add this to end of body tag in 'base.html.twig' file:**
```html
<script type="text/javascript" src="{{ asset('bundles/pouyasoftsdate/lib/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('bundles/pouyasoftsdate/lib/bootstrap-datepicker/bootstrap-datepicker.fa.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".jSDate").datepicker({isRTL: true, dateFormat: "yy/m/d", changeMonth: true, changeYear: true});
    });
</script>
```

**Add this to 'app/config.yml' file:**
```yaml
twig:
    form_themes:
        - 'PouyaSoftSDateBundle:Form:form_s_date.html.twig'
```

**References:**
- [Bootstrap Jalali Datepicker](http://mousavian.github.io/bootstrap-jalali-datepicker/)