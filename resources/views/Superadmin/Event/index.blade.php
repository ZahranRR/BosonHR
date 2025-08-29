@extends('layouts.app')
@section('title', 'Calender/index')
@section('content')
    <meta name="csrf_token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css"
        integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <div class="container">
        <div class="row">
            <div class="col-12 mt-3">
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    <div id="modal-action" class="modal" tabindex="-1">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.15/index.global.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"
        integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const modal = $('#modal-action')
        const csrfToken = $('meta[name="csrf_token"]').attr('content');

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap5',
                events: '{{ route('events.list') }}',
                editable: true,
                dateClick: function(info) {
                    $.ajax({
                        url: '{{ route('event.create') }}',
                        data: {
                            start_date: info.dateStr,
                            end_date: info.dateStr,
                        },
                        success: function(res) {
                            modal.html(res);
                            var modalInstance = new bootstrap.Modal(modal[0]);
                            modalInstance.show();

                            // Inisialisasi Flatpickr saat modal sudah muncul
                            modal.on('shown.bs.modal', function() {
                                $('.datepicker').flatpickr({
                                    todayHighlight: true,
                                    dateFormat: "Y-m-d"
                                });
                            });

                            $('#form-action').on('submit', function(e) {
                                e.preventDefault();
                                const form = this;
                                const formData = new FormData(form);
                                $.ajax({
                                    url: form.action,
                                    method: form.method,
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(res) {
                                        modalInstance.hide();
                                        calendar.refetchEvents();
                                    }
                                });
                            });
                        }
                    });
                },

                eventClick: function({
                    event
                }) {
                    $.ajax({
                        url: '{{ route('event.edit', ':id') }}'.replace(':id', event
                            .extendedProps.event_id),
                        success: function(res) {
                            modal.html(res);
                            var modalInstance = new bootstrap.Modal(modal[0]);
                            modalInstance.show();

                            // Inisialisasi flatpickr pada modal yang sudah terbuka
                            modal.on('shown.bs.modal', function() {
                                $('.datepicker').flatpickr({
                                    todayHighlight: true,
                                    dateFormat: "Y-m-d"
                                });
                            });

                            $('#form-action').on('submit', function(e) {
                                e.preventDefault();
                                const form = this;
                                const formData = new FormData(form);
                                $.ajax({
                                    url: form.action,
                                    method: form.method,
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(res) {
                                        modalInstance.hide();
                                        calendar.refetchEvents();
                                    }
                                });
                            });
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                },

                eventDrop: function(info) {
                    const event = info.event;
                    $.ajax({
                        url: '{{ route('event.update', ':id') }}'.replace(':id', event
                            .extendedProps.event_id),
                        method: 'PUT',
                        data: {
                            event_id: event.extendedProps.event_id,
                            start_date: event.startStr,
                            end_date: event.end ? event.end.toISOString().substring(0, 10) :
                                event.startStr,
                            title: event.title,
                            category: event.extendedProps.category
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            accept: 'application/json'
                        },
                        success: function(res) {
                            iziToast.success({
                                title: 'Success',
                                message: res.message,
                                position: 'topRight'
                            });
                        },
                        error: function(res) {
                            const message = res.responseJSON.message;
                            event.revert();
                            iziToast.error({
                                title: 'Error',
                                message: message ?? 'Something Wrong',
                                position: 'topRight'
                            });
                        }
                    });
                },

                eventResize: function(info) {
                    const {
                        event
                    } = info;
                    $.ajax({
                        url: '{{ route('event.update', ':id') }}'.replace(':id', event
                            .extendedProps.event_id),
                        method: 'PUT',
                        data: {
                            event_id: event.extendedProps.event_id,
                            start_date: event.startStr,
                            end_date: event.end ? event.end.toISOString().substring(0, 10) :
                                event.startStr,
                            title: event.title,
                            category: event.extendedProps.category
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            accept: 'application/json'
                        },
                        success: function(res) {
                            iziToast.success({
                                title: 'Success',
                                message: res.message,
                                position: 'topRight'
                            });
                        },
                        error: function(res) {
                            const message = res.responseJSON.message;
                            info.revert();
                            iziToast.error({
                                title: 'Error',
                                message: message ?? 'Something Wrong',
                                position: 'topRight'
                            });
                        }
                    });
                }
            });
            calendar.render();
        });
    </script>

    <style>
        /* Menghilangkan garis bawah dari semua link (<a>) di seluruh halaman */
        a {
            text-decoration: none !important;
        }
    </style>
@endsection
