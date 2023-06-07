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
  var authorSelect = form.find('select[name="author"]');
  var reviewerSelect = form.find('select[name="reviewer"]');
  var submitButton = form.find('input[type="submit"]');
  console.log(authorSelect);
  submitButton.on('click', function(event) {
    var selectedAuthor = authorSelect.val();
    var selectedReviewer = reviewerSelect.val();

    if (selectedAuthor === selectedReviewer) {
      alert('Author and reviewer cannot be the same!');
      event.preventDefault();
    }
  });
  form.dialog({
    modal: true,
    width: 400,
    title: 'Add New Event',
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
  var authorSelect = form.find('select[name="author"]');
  var reviewerSelect = form.find('select[name="reviewer"]');
  var submitButton = form.find('input[type="submit"]');
  console.log(authorSelect);
  submitButton.on('click', function(event) {
    var selectedAuthor = authorSelect.val();
    var selectedReviewer = reviewerSelect.val();

    if (selectedAuthor === selectedReviewer) {
      alert('Author and reviewer cannot be the same!');
      event.preventDefault();
    }
  });
  form.dialog({
    modal: true,
    width: 400,
    title: 'Edit Form',
    open: function () {
        
    },
    close: function() {
      jQuery(this).dialog('destroy');
    }
  });
}
function showToday() {
  var currentDate = new Date();
  var currentMonth = currentDate.getMonth() + 1;
  var currentYear = currentDate.getFullYear();
  
  document.getElementById('calendar_month').value = currentMonth;
  document.getElementById('calendar_year').value = currentYear;
  
  var form = document.getElementsByClassName('select-month-year');

  form[0].submit();

}