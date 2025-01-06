@extends('layouts.app')

@section('head')
<style>
    @guest
        .navbar {
            display: none;
        }

        .school-navbar {
            display: flex;
        }
    @endguest
</style>
@endsection

@section('content')

    <a href="{{ $cover->host->promo_image_url() }}" class="welcome-image-popup">
        <img src="{{ $cover->host->promo_image_url() }}" onerror="this.onerror=null;this.src='';" class="welcome-image d-none">
    </a>

    @guest
        <div class="container-fluid">
            <div class="row mb-3">
                <nav class="navbar navbar-light school-navbar">
                    <div class="container">
                        @if($cover->host->website)
                            <a href="{{ $cover->host->website }}" target="_blank" class="navbar-brand m-auto">
                                <img src="{{ $cover->host->bucket . '/WebImgs/' . $cover->host->name . '.png' }}" alt="{{ $cover->host->name }}" class="">
                            </a>
                        @else
                            <div class="navbar-brand m-auto">
                                <img src="{{ $cover->host->bucket . '/WebImgs/' . $cover->host->name . '.png' }}" alt="{{ $cover->host->name }}" class="">
                            </div>
                        @endif
                    </div>
                </nav>
            </div>
        </div>
    @endguest

    <div class="container">

        <section id="cover-section">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-6 cover-col">
                    <div class="cover-info-wrapper text-center">
                        <h3>{{ $cover->name }}</h3>
                        <hr>
                        <img src="{{ $cover->image_url() }}" alt="{{ $cover->name }}">
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-6 cover-col">
                    <div class="cover-topics-wrapper">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center"><h4>TOPICS</h4></th>
                                    <th width="150"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ( \App\Models\Cover::parent_cover($cover)->topics()->orderBy('sequence', 'ASC')->get() as $topic)
                                    <tr>
                                        <td style="vertical-align: middle">{{ $topic->name }}</td>
                                        <td>
                                            @if ($topic->videos->count())
                                                <button type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Videos" data-trigger="content-canvas" data-topic="{{ $topic->name }}" data-action="video" data-topic-id="{{ $topic->id }}" class="btn btn-primary">V</button>
                                            @endif
                                            @if ($topic->audios->count())
                                                <button type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Audios" data-trigger="content-canvas" data-topic="{{ $topic->name }}" data-action="audio" data-topic-id="{{ $topic->id }}" class="btn btn-info">A</button>
                                            @endif
                                            @if ($topic->worksheets->count())
                                                <button type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Worksheets" data-trigger="content-canvas" data-topic="{{ $topic->name }}" data-action="worksheet" data-topic-id="{{ $topic->id }}" class="btn btn-warning">W</button>
                                            @endif
                                            @if ($topic->tests->count())
                                                <button type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Tests" data-trigger="content-canvas" data-topic="{{ $topic->name }}" data-action="test" data-topic-id="{{ $topic->id }}" class="btn btn-success">T</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @guest
        @if ( ! empty($cover->host->pgfootertxt) )
            <footer class="text-center text-lg-start bg-light text-muted">
                <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
                    <a class="text-reset fw-bold" href="{{ $cover->host->pgfooterurl }}">{{ $cover->host->pgfootertxt }}</a>
                </div>
            </footer>
        @endif
    @endguest

    <div class="offcanvas offcanvas-end" tabindex="-1" id="videoWorksheetOffcanvas" aria-labelledby="videoWorksheetOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="videoWorksheetOffcanvasLabel">CONCEPT <span id="canvas-action"></span><br>Topic - <span id="canvas-topic"></span></h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">

        </div>
      </div>

    <div class="modal fade" id="worksheetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-auto">
                    <iframe src="" style="border:0;width:720px;height:405px" allow="encrypted-media" class="worksheet-iframe" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    jQuery(document).ready(function() {

        $('.welcome-image-popup').magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            image: {
                markup: '<div class="mfp-figure welcome-img">'+
                            '<div class="mfp-close"></div>'+
                            '<div class="mfp-img mfp-welcome-img"></div>'+
                            '<div class="mfp-bottom-bar">'+
                            '<div class="mfp-title"></div>'+
                            '<div class="mfp-counter"></div>'+
                            '</div>'+
                        '</div>'
            }
        });

        setTimeout(() => {
            const welcomeImg = $('.welcome-image').attr('src')
            if ( welcomeImg ) {
                $('.welcome-image-popup').trigger('click')
            }
        }, 1000);

        var videoWorksheetCanvas = new bootstrap.Offcanvas(document.getElementById('videoWorksheetOffcanvas'))

        function buildVideoHtml(videos) {
            let html = '<ul class="list-group">';

            videos.forEach(video => {
                html += `
                    <li class="list-group-item">
                        <div class="d-flex gap-1">
                            <h4 class="video-name cursor-pointer" data-id="${video.id}">${video.menu_name}</h4>
                        </div>
                    </li>
                `;
            })

            html += '</ul>';

            $('.offcanvas-body').html(html);

        }

        function buildAudioHtml(audios, url) {
            let html = '<ul class="list-group">';

            audios.forEach(audio => {
                html += `
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center gap-1">
                            <h4 class="audio-name cursor-pointer">${audio.menu_name}</h4>
                            <div class="audio-play-icon"><i class="fa fa-play"></i></div>
                        </div>
                        <div class="audio-file">
                            <audio src="${url}/${audio.file_name}" controls controlsList="nodownload" style="display: none"></audio>
                        </div>
                    </li>
                `;
            })

            html += '</ul>';

            $('.offcanvas-body').html(html);

        }

        function buildWorkSheetHtml(worksheets, url) {
            let html = '<ul class="list-group">';

            worksheets.forEach(worksheet => {
                html += `
                    <li class="list-group-item">
                        <div class="d-flex gap-1">
                            <h4 class="worksheet-name cursor-pointer" data-worksheet="${worksheet.id}">${worksheet.menu_name}</h4>
                        </div>
                    </li>
                `;
            })

            html += '</ul>';

            $('.offcanvas-body').html(html);
        }

        function buildTestHtml(tests, url) {
            let html = '<ul class="list-group">';

            tests.forEach(test => {
                html += `
                    <li class="list-group-item">
                        <div class="d-flex gap-1">
                            <h4 class="test-name cursor-pointer" data-test="${test.id}">${test.menu_name}</h4>
                        </div>
                    </li>
                `;
            })

            html += '</ul>';

            $('.offcanvas-body').html(html);
        }

        $('[data-trigger="content-canvas"]').on('click', function(e) {
            e.preventDefault();
            const $btn = $(this)
            const topicId = $btn.data('topic-id')
            const action = $btn.data('action')
            const topic = $btn.data('topic')

            $('#canvas-action').html(action == 'video' ? 'VIDEOS' : (action == 'audio' ? 'AUDIO' : 'WORKSHEETS'))
            $('#canvas-topic').html(topic)

            let url = '{{ route("user.cover.get.video_worksheet", ["aaaaa", "00000"]) }}';
            url = url.replace('aaaaa', action)
            url = url.replace('00000', topicId)

            $.ajax({
                url: url,
                method: 'GET',
                beforeSend: function() {
                    $btn.addClass('disabled')
                },
                complete: function() {
                    $btn.removeClass('disabled')
                },
                success: function(res) {
                    if ( ! res.data.length ) {
                        alert('Invalid request.');
                        return false;
                    }

                    let showCanvas = true

                    if (action == 'video') {
                        buildVideoHtml(res.data)
                        if ( $('.video-name').length === 1 ) {
                            $('.video-name').trigger('click')
                            showCanvas = false
                        }
                    } else if (action == 'audio') {
                        buildAudioHtml(res.data, res.url)
                        if ( $('.audio-play-icon').length === 1 ) {
                            $('.audio-play-icon').trigger('click')
                        }
                    } else if (action == 'test') {
                        buildTestHtml(res.data, res.url)
                        if ( $('.test-name').length === 1 ) {
                            $('.test-name').trigger('click')
                            showCanvas = false
                        }
                    } else {
                        buildWorkSheetHtml(res.data, res.url)
                        if ( $('.worksheet-name').length === 1 ) {
                            $('.worksheet-name').trigger('click')
                            showCanvas = false
                        }
                    }

                    if (showCanvas) {
                        // if canvas has only one video/worksheet show that directly
                        videoWorksheetCanvas.show();
                    }
                }
            })
        })

        $(document).on('click', '.video-name', function(e) {
            const id = $(this).attr('data-id');
            const text = $(this).html();

            let url = '{{ route("user.cover.get.video", "VIDEO_ID") }}';
            url = url.replace('VIDEO_ID', id)

            $.ajax({
                url: url,
                beforeSend: function() {},
                success: function(res) {
                    if (res.success) {
                        $.magnificPopup.open({
                            items: {
                                src: `
                                        <div class="popup-header">
                                            <h3 class="popup-title">${text}</h3>
                                            <div class="close-popup"><i class="fa fa-times"></i></div>
                                        </div>
                                        <iframe src="${res.url}" style="border:0;width:720px;height:405px" allow="encrypted-media" class="video-iframe" allowfullscreen></iframe>
                                    `,
                                type: 'inline'
                            },
                            modal: true,
                        });
                    } else {
                        alert(res.message)
                    }
                }
            })
        })

        function pauseAllAudios() {
            $('.audio-play-icon').each(function() {
                if ( ! $(this).is(':visible') ) {
                    $(this).parent().next().find('audio').get(0).pause()
                }
            })
        }

        $('#videoWorksheetOffcanvas').on('hidden.bs.offcanvas', pauseAllAudios)

        $(document).on('click', '.audio-play-icon', function(e) {
            pauseAllAudios();

            var audio = $(this).parent().next().find('audio').show().get(0);
            audio.play();
            $(this).hide();

            audio.onpause = function(e) {
                $self = $(e.target);
                $self.hide();
                $self.parent().prev().find('.audio-play-icon').show();
            }
        })

        $(document).on('click', '.worksheet-name, .test-name', function(e) {
            $('#worksheetModal .modal-title').html($(this).html());

            let url = '{{ route("user.pdf.viewer.index", ["11111", "type" => "test"]) }}'
            url = url.replace('11111', $(this).data('test'))

            if ( $(this).hasClass('worksheet-name') ) {
                url = '{{ route("user.pdf.viewer.index", ["11111", "type" => "worksheet"]) }}'
                url = url.replace('11111', $(this).data('worksheet'))
            }

            $('#worksheetModal iframe').attr('src', url);
            $('#worksheetModal').modal('show')
        })

        $('#worksheetModal').on('hidden.bs.modal', function() {
            $('#worksheetModal iframe').attr('src', '');
        })

        $(document).on('click', '.close-popup', function() {
            $.magnificPopup.close();
        });

    })
</script>
@endsection
