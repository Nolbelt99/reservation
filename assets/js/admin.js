require('../scss/admin.scss');

const $ = require('jquery');
global.$ = global.jQuery = $;
require('@coreui/coreui');
require('jquery');
require('jquery-ui');
require('jquery.iframe-transport');
require('bootstrap-datepicker');
require('bootstrap-datepicker/dist/locales/bootstrap-datepicker.hu.min');
require('symfony-collection/jquery.collection');
$('.sticky-notify').first().delay(6000).fadeOut();
require('../../public/bundles/fosckeditor/ckeditor');
import "../css/calendar.css";

$('.ReservationItemsCollection').collection({
    allow_up: false,
    allow_down: false,
    allow_add: true,
    allow_remove: true,
    allow_duplicate: false,
    add_at_the_end: true,
    fade_in: false,
    fade_out: false,
    min: 0,
    init_with_n_elements: 0,
    add: '<a href="#" class="btn btn-sm btn-info">Szolgáltatás hozzáadása</a>',
    before_remove: function(collection, element) { return confirm("Biztos?"); },
});

$('.datepicker').datepicker({
    format: 'yyyy.mm.dd',
    weekStart: 1,
    language: 'hu'
});

