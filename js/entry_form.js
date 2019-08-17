//make sure all picks have a checked value
function checkform() {
        var f = document.entryForm;
        var allChecked = true;
        var allR = document.getElementsByTagName('input');
        for (var i=0; i < allR.length; i++) {
                if(allR[i].type == 'radio') {
                        if (!radioIsChecked(allR[i].name)) {
                                allChecked = false;
                        }
                }
    }
    if (!allChecked) {
                return confirm('One or more picks are missing for the current week.  Do you wish to submit anyway?');
        }
        return true;
}
function radioIsChecked(elmName) {
        var elements = document.getElementsByName(elmName);
        for (var i = 0; i < elements.length; i++) {
                if (elements[i].checked) {
                        return true;
                }
        }
        return false;
}
function checkRadios() {
  $('input[type=radio]').each(function(){
    var targetLabel = $('label[for="'+$(this).attr('id')+'"]');
    console.log($(this).attr('id')+': '+$(this).is(':checked'));
    if ($(this).is(':checked')) {
     targetLabel.addClass('highlight');
    } else {
      targetLabel.removeClass('highlight');
    }
  });
}
$(function() {
        checkRadios();
        $('input[type=radio]').click(function(){
          checkRadios();
        });
        $('label').click(function(){
          checkRadios();
        });
});

