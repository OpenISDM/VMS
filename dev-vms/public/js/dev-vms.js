$(function() {
    $( "#startdatepicker" ).datepicker({
            format: "yyyy-mm-dd",
            //language: "zh-TW",
            todayHighlight: true
    });
});

$(function() {
    $( "#enddatepicker" ).datepicker({
            format: "yyyy-mm-dd",
            //language: "zh-TW",    
            todayHighlight: true
    });
});

$(function() {
    if ($('#is-ongoing').is(':checked') == true) {
        $('#enddatepicker').prop('disabled', true);
    } else {
        $('#enddatepicker').prop('disabled', false);
    }
});

$('#is-ongoing').change(function() {
    if ($('#is-ongoing').is(':checked') == true) {
        $('#enddatepicker').prop('disabled', true);
    } else {
        $('#enddatepicker').prop('disabled', false);
    }
});