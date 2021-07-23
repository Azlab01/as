const mix = require('laravel-mix');
const calendar = require('@fullcalendar/core');
const  dayGridPlugin = require('@fullcalendar/daygrid');
 /**************************** */
//--          CSS           --//
 /************************** */
 mix.styles([
    'public/bootstrap.min.css',
    'public/Chart.min.css',
    'public/daterangepicker.css',
], 'public/app.css').version();



/**************************** */
//--        JS             --//
/************************** */
mix.scripts([
'public/jquery.min.js',
'public/moment.min.js',
'public/daterangepicker.js',
], 'public/app.js')
.version();

