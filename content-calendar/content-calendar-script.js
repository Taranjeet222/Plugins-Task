// function showForm(formId){
//   console.log(1);
//   console.log(formId);
//   var form = document.getElementById(formId);
//   form.classList.remove('hide');
// }
function showForm(formId) {
  console.log(1);
  console.log(formId);
  var form = jQuery('#' + formId);
  form.dialog({
    modal: true,
    width: 400,
    open: function () {
      
    },
    close: function() {
      jQuery(this).dialog('destroy');
    }
  });
}
function ShowEditForm(formId) {
  console.log(1);
  console.log(formId);
  var form = jQuery('#' + formId);
  form.dialog({
    modal: true,
    width: 400,
    open: function () {
        
    },
    close: function() {
      jQuery(this).dialog('destroy');
    }
  });
}