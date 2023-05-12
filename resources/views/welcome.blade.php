<!doctype html>
<html lang="en">

<head>
    <title>Test - Project</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/welcome.css') }}">
</head>

<body>
    <div class="container-fluid mt-3">
        <div class="loader-container d-none
        ">
            <div class="loader"></div>
        </div>
        <div class="form-group">
            <button class="btn btn-info" id="select-file">
                <span class="fa fa-add"></span>
                Choose File(.csv)
            </button>
            <button class="btn btn-primary d-none" id="read-file">
                <span class="fa fa-upload"></span>
                Read File
            </button>
            <label class="label label-default file-name d-none">
                <span class="text"></span>
                <span class="fa fa-close"></span>
            </label>
            <input type="file" class="d-none" accept=".csv" />
        </div>
        <form class="form" id="filter-form">
            <div class="row">
                <div class="col-md-2 form-group">
                    <label for="category">Category:</label>
                    <input type="text" id="category" class="form-control" />
                </div>
                <div class="col-md-2 form-group">
                    <label for="gender">Gender:</label>
                    <select class="form-control" id="gender">
                        <option value="">-- Select Gender --</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label for="birthday">Date of Birth:</label>
                    <input type="date" id="birthday" class="form-control" />
                </div>
                <div class="col-md-2 form-group">
                    <label for="age">Age:</label>
                    <input type="number" id="age" class="form-control" />
                </div>
                <div class="col-md-2 form-group">
                    <label for="age_range">Age range:</label>
                    <input type="text" id="age_range" placeholder="ex:25 - 30" class="form-control" />
                </div>
                <div class="col-md-2 form-group pt-4">
                    <button class="btn btn-primary">
                        <span class="fa fa-search"></span>
                        Apply Filters
                    </button>
                    <button class="btn btn-success export-csv" type="button">
                        <span class="fa fa-file-csv"></span>
                        Export CSV
                    </button>
                </div>
            </div>
        </form>
        <table id="datatable" class="table table-reponsive table-stripped table-bordered">
            <thead>
                <th>No</th>
                <th>Category</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Date of Birth</th>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script style="text/javascript">
        const ReadCsv = function() {
            const _token = $('meta[name="csrf-token"]').attr('content');
            // Load datatable
            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('users.getUsers') }}",
                    type: 'POST',
                    data: function(d) {
                        d._token = _token;
                        d.category = $("#category").val().trim();
                        d.gender = $("#gender").val();
                        d.birthday = $("#birthday").val();
                        d.age = $("#age").val();
                        d.age_range = $("#age_range").val().trim();
                    }
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'category'
                    },
                    {
                        data: 'firstname'
                    },
                    {
                        data: 'lastname'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'gender'
                    },
                    {
                        data: 'birthdate'
                    }
                ],
                "lengthMenu": [5, 10, 25, 50],
                "pageLength": 10,
                "order": [
                    [1, 'asc']
                ],
                "searching": false
            });

            const loading = function(flag) {
                const loader = $(".loader-container");
                if ($(".loader-container").hasClass('d-none') && flag) {
                    loader.removeClass('d-none');
                } else {
                    setTimeout(() => loader.addClass('d-none'), 500);
                }
            }
            const uploadCsvFile = function(file) {
                const formData = new FormData();
                formData.append('_token', _token);
                formData.append('csv_file', file);
                loading(true);
                $.ajax({
                    url: "{{ route('users.import') }}",
                    type: "POST",
                    data: formData,
                    processData: false,

                    contentType: false,
                    success: function(response) {
                        loading(false);
                        table.ajax.reload();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        loading(false);
                        console.error(jqXHR.responseJSON);
                    }
                })
            }
            const eventBinding = function() {

                $('#filter-form').on('submit', function(e) {
                    e.preventDefault();
                    table.ajax.reload();
                });

                $("#select-file").click(function() {
                    $("input[type='file']").click();
                });
                $("input[type='file']").change(function() {
                    const file = $(this)[0].files[0];
                    $("#read-file").removeClass('d-none');
                    $("label.file-name").removeClass('d-none').find('span.text').text(file.name);
                });
                $("span.fa-close").click(function() {
                    $("input[type='file']").val('');
                    $("#read-file").addClass('d-none');
                    $("label.file-name").addClass('d-none').find('span.text').text('');
                });
                $("#read-file").click(function() {
                    uploadCsvFile($("input[type='file']")[0].files[0]);
                    // hide <Read file> button
                    $("input[type='file']").val('');
                    $("#read-file").addClass('d-none');
                    $("label.file-name").addClass('d-none').find('span.text').text('');
                });
                $(".export-csv").click(function() {
                    loading(true);
                    const data = {
                        _token: _token,
                        category: $("#category").val().trim(),
                        gender: $("#gender").val(),
                        birthday: $("#birthday").val(),
                        age: $("#age").val(),
                        age_range: $("#age_range").val().trim()
                    };
                    $.get({
                        url: "{{ route('users.export') }}",
                        data,
                        success: function (data) {
                            loading(false);
                            // Create a temporary anchor element to download the CSV file
                            var downloadLink = document.createElement('a');
                            downloadLink.href = 'data:text/csv;charset=utf-8,' + encodeURI(data);
                            downloadLink.download = 'export.csv';
                            // Trigger a click event on the anchor element to download the CSV file
                            downloadLink.click();
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            loading(false);
                        },
                    });
                });
            }
            return {
                init: function() {
                    eventBinding();
                }
            }
        }();
        $(document).ready(function() {
            ReadCsv.init();
        });
    </script>
</body>

</html>
