pouyasoft_ir/simple-date-bundle
========================
**A bundle for persian date in Symfony2**

Install
--------------
* **Add to Composer:**

```json
"require": {
    "pouyasoft_ir/simple-date-bundle": "*",
}
```

* **Add to AppKernel:**

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

* **install assets:** 

```
php app/console assets:install
```

Service
--------------
**Service Name:** pouya_soft.j_sdate_service

**Functions:**
* **MiladiToShamsi:** Convert Miladi (DateTime) To Shamsi (String). (Parameter: separator - default: /)
* **ShamsiToMiladi:** Convert Shamsi (String) To Miladi (DateTime). (Parameter: separator - default: /)
* **getWeekDay:**     Return WeekDay.
* **isLeapYear:**     Return true or false.

**Example:**
```php
$shamsiString = $this->get('pouya_soft.j_sdate_service')->MiladiToShamsi(new \DateTime());
//result: 1394/11/22
$shamsiString = $this->get('pouya_soft.j_sdate_service')->MiladiToShamsi(new \DateTime(),'-');
//result: 1394-11-22

$miladiDate = $this->get('pouya_soft.j_sdate_service')->ShamsiToMiladi('1394/11/22');
$miladiDate = $this->get('pouya_soft.j_sdate_service')->ShamsiToMiladi('1394-11-22','-');
```

Twig
--------------
**Functions:**
* **jSDate:** Convert Miladi (DateTime) To Shamsi (String).  (Parameter: separator - default: /)

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
* Bootstrap
* Jquery

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
        $(".jSDate").datepicker({isRTL: true, dateFormat: "yy/m/d"});
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
* [Bootstrap Jalali Datepicker](http://mousavian.github.io/bootstrap-jalali-datepicker/)