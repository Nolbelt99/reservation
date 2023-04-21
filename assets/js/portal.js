require('../scss/portal.scss');

const $ = require('jquery');
global.$ = global.jQuery = $;
require('@coreui/coreui');
require('jquery');
require('jquery-ui');
require('bootstrap-datepicker');
require('bootstrap-datepicker/dist/locales/bootstrap-datepicker.hu.min');
import "../css/calendar.css";


$('.js-datepicker').datepicker({
  format: 'yyyy.mm.dd',
  weekStart: 1,
  language: 'hu'
});
