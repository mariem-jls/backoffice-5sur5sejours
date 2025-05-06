/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import 'jquery';
import moment from 'moment';
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';

$(function() {
    $('#daterange').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: moment().subtract(29, 'days'),
        endDate: moment()
    }, function(start, end, label) {
        // Update the hidden inputs with the selected date range
        $('#datedebut').val(start.format('YYYY-MM-DD'));
        $('#datefin').val(end.format('YYYY-MM-DD'));
    });

    // Initialize the hidden inputs with the default date range
    var start = $('#daterange').data('daterangepicker').startDate;
    var end = $('#daterange').data('daterangepicker').endDate;
    $('#datedebut').val(start.format('YYYY-MM-DD'));
    $('#datefin').val(end.format('YYYY-MM-DD'));
});