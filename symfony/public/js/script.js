$('#confirm-part-delete').on('show.bs.modal', function(e) {
   $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
  // console.log('test', $(e.relatedTarget).data('href'));
});