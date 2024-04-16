mix.js('resources/js/app.js', 'public/js')
    .vue()  //..verify this!
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);