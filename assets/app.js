/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

import $ from 'jquery';

function accordion() {
    $('.accordion-header').on('click', function () {
        const accordion = $(this).closest('.accordion');
        const accordionCollapse = $(this).next('.accordion-collapse');

        if (accordionCollapse.hasClass('show')) {
            accordionCollapse.removeClass('show');

            return;
        }

        accordion.find('.accordion-collapse.show').removeClass('show');

        accordionCollapse.addClass('show');
    });

    $('.modal-open').on('click', function () {
        const targetModal = $(this).attr('data-target-modal');

        $(targetModal).show();
    });

    $('.modal-close').on('click', function () {
        $(this).closest('.modal').hide();
    });
}

function selector() {
    var active = false;
    var photos = [];

    $('#selector').on('click', function () {
        $(this).text('Seçimi İptal Et');
        active = !active;

        if (active) {
            $(this).text('Seçimi İptal Et');
        } else {
            $(this).text('Seç');
        }

        $(this).toggleClass('btn-primary btn-danger');
        $('#select-action').toggleClass('hidden inline-flex');
    });

    $('.photo').on('click', function () {
        if (!active)
            return;

        $(this).attr('data-selected', !$(this).attr('data-selected'));

        $(this).toggleClass('bg-gray-100');

        var index = photos.indexOf($(this).attr('data-id'));

        if (index < 0) {
            photos.push($(this).attr('data-id'));
        } else {
            photos.splice(index, 1);
        }

        $('#counter').text('Seçilenleri (' + photos.length + '):');

        console.log(photos);
    });

    $('#make-came').on('click', function (e) {
        e.preventDefault();

        if (!active)
            return;

        $('.photo').each(function () {
            var element = $(this);

            element.attr('data-selected', false);
            element.removeClass('bg-gray-100');

            if (!photos.includes(element.attr('data-id')))
                return;

            if (element.attr('data-came') == 1)
                return;

            $.post($('#photos').attr('data-post-url'), {
                photoId: element.attr('data-id'),
                came: 1
            }, function () {
                element.removeClass('border-red-200 bg-red-100');
            });
        });

        $('#success-result').text(photos.length + ' fotoğraf güncellendi.').removeClass('hidden');
        photos = [];
        $('#counter').text('Seçilenleri (' + photos.length + '):');
        $('#selector').text('Seç');
        $('#selector').toggleClass('btn-primary btn-danger');
        $('#select-action').toggleClass('hidden inline-flex');
        active = false;

        setTimeout(function () {
            $('#success-result').addClass('hidden');
        }, 3000);
    });

    $('#make-did-not-come').on('click', function (e) {
        e.preventDefault();

        if (!active)
            return;

        $('.photo').each(function () {
            var element = $(this);

            element.attr('data-selected', false);
            element.removeClass('bg-gray-100');

            if (!photos.includes(element.attr('data-id')))
                return;

            if (element.attr('data-came') == 0)
                return;

            $.post($('#photos').attr('data-post-url'), {
                photoId: element.attr('data-id'),
                came: 0
            }, function () {
                element.addClass('border-red-200 bg-red-100');
            });
        });

        $('#success-result').text(photos.length + ' fotoğraf güncellendi.').removeClass('hidden');
        photos = [];
        $('#counter').text('Seçilenleri (' + photos.length + '):');
        $('#selector').text('Seç');
        $('#selector').toggleClass('btn-primary btn-danger');
        $('#select-action').toggleClass('hidden inline-flex');
        active = false;

        setTimeout(function () {
            $('#success-result').addClass('hidden');
        }, 3000);
    });
}

function adminDealerForm() {
    var $dealer = $('#download_dealer');
    // When sport gets selected ...
    $dealer.change(function () {
        // ... retrieve the corresponding form.
        var $form = $(this).closest('form');
        // Simulate form data, but only include the selected sport value.
        var data = {};
        data[$dealer.attr('name')] = $dealer.val();
        // Submit data via AJAX to the form's action path.
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            complete: function (html) {
                // Replace current position field ...
                $('#download_school').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html.responseText).find('#download_school')
                );
                // Position field now displays the appropriate positions.
            }
        });
    });
}

$(function () {
    $('.add-row').on('click', function () {
        var list = $($(this).attr('data-list-selector'));

        var counter = list.data('widget-counter') || list.children().length;

        var widget = list.attr('data-prototype');

        widget = $(widget.replace(/__name__/g, counter));

        counter++;

        list.data('widget-counter', counter);

        var widgetTags = list.attr('data-widget-tags');

        if (widgetTags) {
            widget = $(widgetTags).html(widget);
        }


        console.log(widget);
        widget.appendTo(list);
    });

    $(document).on('click', '.del-row', function () {
        var row = $(this).closest($(this).attr('data-row-selector'));

        row.remove();
    });

    accordion();

    selector();

    $('#form_school').on('change', function () {
        var form = $(this).closest('form');

        var data = {};
        data[$(this).attr('name')] = $(this).val();

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: data,
            complete: function (html) {
                $('#form_classroom').replaceWith(
                    $(html.responseText).find('#form_classroom')
                );
            }
        });
    });

    $('#filter_student #form_school').on('change', function () {
        var form = $(this).closest('form');

        var data = {};
        data[$(this).attr('name')] = $(this).val();

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: data,
            complete: function (html) {
                $('#form_classroom').replaceWith(
                    $(html.responseText).find('#form_classroom')
                );
            }
        });
    });

    $('#yearbook')
        .on('change', '#yearbook_dealer', function () {
            var form = $(this).closest('form');

            var data = {};
            data[$(this).attr('name')] = $(this).val();

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: data,
                complete: function (html) {
                    $('#yearbook_school').replaceWith(
                        $(html.responseText).find('#yearbook_school')
                    );
                }
            });
        })
        .on('change', '#yearbook_school', function () {
            console.log('DEĞİŞİYOR');
            var form = $(this).closest('form');

            var data = {};
            data[$(this).attr('name')] = $(this).val();

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: data,
                complete: function (html) {
                    $('#yearbook_classrooms').replaceWith(
                        $(html.responseText).find('#yearbook_classrooms')
                    );

                    $('#yearbook_profileAlbum').replaceWith(
                        $(html.responseText).find('#yearbook_profileAlbum')
                    );

                    $('#yearbook_galleryAlbums').replaceWith(
                        $(html.responseText).find('#yearbook_galleryAlbums')
                    );
                }
            });
        });

    adminDealerForm();
});