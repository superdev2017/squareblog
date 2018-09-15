$( document ).ready(function() {
  applyHelpers();

  $( "a, img, button" ).unbind();

  $(document).on('click', '.editable-img', function(){
    $('#new_image').attr('data-replace', $(this).attr('data-target'));
    $('#img_numb').text($(this).attr('data-target'));

    var target = $('#new_image').attr('data-replace');
    var elem = $(".image_tag_"+target);

    $edit_img = $('.editor-bar .edit-image');

    $edit_img.find('.fileinput .fileinput-filename').text(elem.attr('src'));
    $edit_img.find('#new_image_width').val(elem.width());
    $edit_img.find('#new_image_height').val(elem.height());

    $edit_img.find('.show-image').hide();
    $edit_img.find('.hide-image').hide();

    if (elem.is(':hidden')) {
      $edit_img.find('.show-image').show();
    } else {
      $edit_img.find('.hide-image').show();
    }

    $edit_img.show();
  });

  $("#new_image_width").on('keyup change', function (){
    elem = getImageElement();
    elem.width($(this).val());
  });

  $("#new_image_height").on('keyup change', function (){
    elem = getImageElement();
    elem.height($(this).val());
  });
  $("#new_image_align").on('change', function (){
    elem = getImageElement();
    elem.css('display', 'inline-block');
    elem.parent().css('text-align',  $(this).val());
    elem.parent().css('width', '100%');
  });

  $(".closer").click(function() {
    $('.editor-bar .edit-image').hide();
  });

  $(".hide-image").click(function() {
    var target = $('#new_image').attr('data-replace');
    removeImage(target);
  });
  $(".show-image").click(function() {
    var target = $('#show_image').attr('data-replace');
    showImage(target);
  });

  $("#save-website").click(function() {
    saveWebsite();
  });

  $( ".editable-content div" )
      .mouseenter(function() {
        if (!$(this).hasClass('panel-edit')) {
          $( this ).addClass('edit');
        }
      })
      .mouseleave(function() {
        $( this ).removeClass('edit');
      });


  // Variable to store your files
  var files;
  $('input[type=file]').on('change', function(event) {
    elem = getImageElement();
    elem.hide();

    files = event.target.files;

    var formData = new FormData();
    formData.append("image",files[0]);

    $.ajax({
      url: Routing.generate('api_1.upload_image', { 'api_kind': 's', '_format': 'json', apikey: api_key }),
      data: formData,
      type: 'POST',
      dataType:"JSON",
      cache: false,
      contentType: false,
      processData: false,
      success: function (data) {
        if (data.status == 'completed') {
          replaceImage(elem, data.data.secure_url);
        }
      }
    });
  });


  function replaceImage(elem, url) {
    elem.show();

    if (elem.hasClass('editable-bg-image')) {
      elem.css('background-image', 'url(' + url + ')');
    } else {
      elem.attr('src', url);
    }
  }

  function removeImage(image_id) {
    $edit_img.find('.show-image').show();
    $edit_img.find('.hide-image').hide();

    elem = getImageElement();
    elem.hide();
  }

  function showImage(image_id) {
    $edit_img.find('.show-image').hide();
    $edit_img.find('.hide-image').show();

    elem = getImageElement();
    elem.show();
  }

  function saveWebsite() {
    removeHelpers();
    var html = $('.editable-content').html();
    applyHelpers();

    $.ajax({
      url: Routing.generate('api_1.site_save', { 'api_kind': 's', '_format': 'json', apikey: api_key }),
      data: {
        html: html,
        id: site_id
      },
      type: 'POST',
      dataType:"JSON",
      cache: false,
      success: function (data) {
        if (data.status == 'completed') {
          swal(
              'Good job!',
              'Your changes have been saved',
              'success'
          )
        } else {
          swal(
              'Oops!',
              'An unknown error occurred, please try again.',
              'danger'
          )
        }
      }
    });
  }


  function applyHelpers() {
    // Create editable input tags
    $(".editable-content a, .editable-content p, .editable-content h1, .editable-content h2, .editable-content h3, .editable-content h4, .editable-content h5, .editable-content .menu-item, .editable-content button, .editable-content .editable").each(function()
    {
      $(this).attr('contenteditable', true);
    });

    var image_id = 1;

    $('.has-editable-bg-image').each(function() {
      downwards = '';
      if (image_id == 1) {
        downwards = ' panel-edit-below';
      }
      $(this).prepend('<div data-target="'+image_id+'" class="panel-edit editable-img'+downwards+'">Edit Block</div>');
      $(this).find('.editable-bg-image').addClass('image_tag_' + image_id);
      image_id++;
    });

    $('img').each(function() {
      $(this).parent().prepend('<div data-target="'+image_id+'" class="panel-edit editable-img">Edit Block</div>');
      $(this).addClass('image_tag_' + image_id);
      image_id++;
    });
  }

  function getImageElement() {
    var target = $('#new_image').attr('data-replace');
    return $(".image_tag_"+target);
  }

  function removeHelpers() {
    $(".editable-content a, .editable-content p, .editable-content h1, .editable-content h2, .editable-content h3, .editable-content h4, .editable-content h5, .editable-content .menu-item, .editable-content button").each(function()
    {
      $(this).attr('contenteditable', false);
      $(this).html($(this).text())
    });

    $('.panel-edit').remove();
  }
});
