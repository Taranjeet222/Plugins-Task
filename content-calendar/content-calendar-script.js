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
    open: function () {
        
    },
    close: function() {
      jQuery(this).dialog('destroy');
    }
  });
}
