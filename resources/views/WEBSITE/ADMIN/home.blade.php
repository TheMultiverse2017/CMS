@extends('layouts.WEBSITE.ADMIN.adminApp')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="container py-5">
        <div class="row justify-content-center mb-4 pb-5">
            <div class="col-md-3">
                <div class="card text-bg-danger visits7D">
                    <div class="card-header">
                        Total Visits (7 Days)
                    </div>
                    <div class="card-body analyticsSmallCardBody">
                        {{ $visitCounts['7_days'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-warning visits30D">
                    <div class="card-header">
                        Total Visits (30 Days)
                    </div>
                    <div class="card-body analyticsSmallCardBody">
                        {{ $visitCounts['30_days'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-primary visits60D">
                    <div class="card-header">
                        Total Visits (60 Days)
                    </div>
                    <div class="card-body analyticsSmallCardBody">
                        {{ $visitCounts['60_days'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-success visits1Yr">
                    <div class="card-header">
                        Total Visits (1 Year)
                    </div>
                    <div class="card-body analyticsSmallCardBody">
                        {{ $visitCounts['1_year'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="row justify-content-end mb-4">
                <div class=" col-md-3">
                    <input id="reportrange" class=" form-control">
                    <i class="fa fa-calendar"></i>
                    <span></span> <i class="fa fa-caret-down"></i>
                    </input>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card cardTotalTimeSpentDevice">
                    <div class="card-header">Total Time Spent by Device</div>
                    <div class="card-body">
                        <canvas id="updateDeviceDurationChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card cardTotalDeviceCount">
                    <div class="card-header">Total Device Count</div>
                    <div class="card-body">
                        <canvas id="deviceCountChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="card px-0 mx-0 cardUrlVisitDuration">
                    <div class="card-header">Url Visit / Duration</div>
                    <div class="card-body">
                        <canvas id="topUrlsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            var startDate = moment().subtract(29, 'days');
            var endDate = moment();
            var token = $('meta[name="csrf-token"]').attr('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });
            dateRange('reportrange');

            ajaxAnalytics(startDate, endDate);

            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                ajaxAnalytics(picker.startDate, picker.endDate);
            });

            function ajaxAnalytics(startDate, endDate) {
                start = startDate.format('YYYY-MM-DD');
                end = endDate.format('YYYY-MM-DD');
                route = "{{ route('get.analytics') }}";
                type = 'post';

                $.ajax({
                    type: type,
                    url: route,
                    data: {
                        start: start,
                        end: end,
                    },
                    success: function(response) {
                        if (response.deviceTypes) {
                            updateDeviceCountChart(response.deviceTypes);
                        }
                        if (response.deviceDurations) {
                            updateDeviceDurationChart(response.deviceDurations);
                        }
                        if (response.topUrls) {
                            updateTopUrlsChart(response.topUrls);
                        }

                    }
                });
            }


            function updateDeviceCountChart(data) {
                let ctx = document.getElementById('deviceCountChart').getContext('2d');

                // Convert the data to labels and values
                let labels = Object.keys(data); // e.g., ['Mobile', 'Desktop', 'Tablet']
                let values = Object.values(data); // e.g., [200, 500, 300]

                // Destroy the previous chart instance if it exists
                if (window.updateDeviceCountChart instanceof Chart) {
                    window.updateDeviceCountChart.destroy();
                }

                // Create a new Pie Chart
                window.updateDeviceCountChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Count/s',
                            data: values,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.6)', // Red
                                'rgba(54, 162, 235, 0.6)', // Blue
                                'rgba(255, 206, 86, 0.6)', // Yellow
                                'rgba(75, 192, 192, 0.6)', // Green
                                'rgba(153, 102, 255, 0.6)', // Purple
                                'rgba(255, 159, 64, 0.6)' // Orange
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }

            function updateDeviceDurationChart(data) {
                let ctx = document.getElementById('updateDeviceDurationChart').getContext('2d');

                // Convert the data to labels and values
                let labels = Object.keys(data); // e.g., ['Mobile', 'Desktop', 'Tablet']
                let values = Object.values(data); // e.g., [200, 500, 300]

                // Destroy the previous chart instance if it exists
                if (window.updateDeviceDurationChart instanceof Chart) {
                    window.updateDeviceDurationChart.destroy();
                }

                // Create a new Pie Chart
                window.updateDeviceDurationChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Time in Second/s',
                            data: values,
                            backgroundColor: [
                                'rgba(0, 119, 182, 0.6)', // Deep Blue
                                'rgba(255, 87, 51, 0.6)', // Bright Orange
                                'rgba(123, 239, 178, 0.6)', // Mint Green
                                'rgba(170, 40, 240, 0.6)', // Vivid Purple
                                'rgba(255, 196, 0, 0.6)', // Bold Yellow
                                'rgba(50, 205, 50, 0.6)' // Lime Green
                            ],
                            borderColor: [
                                'rgba(0, 119, 182, 1)',
                                'rgba(255, 87, 51, 1)',
                                'rgba(123, 239, 178, 1)',
                                'rgba(170, 40, 240, 1)',
                                'rgba(255, 196, 0, 1)',
                                'rgba(50, 205, 50, 1)'
                            ],

                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }

            function updateTopUrlsChart(data) {
                let ctx = document.getElementById('topUrlsChart').getContext('2d');

                // Extract URL labels, visit counts, and durations
                let labels = Object.keys(data); // URLs
                let visitCounts = labels.map(url => data[url].count); // Count of visits
                let durations = labels.map(url => data[url].duration); // Duration in seconds

                // Destroy previous instance if exists
                if (window.topUrlsChart instanceof Chart) {
                    window.topUrlsChart.destroy();
                }

                // Create a new Bar Chart
                window.topUrlsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels, // URLs
                        datasets: [{
                                label: 'Visits',
                                data: visitCounts,
                                backgroundColor: 'rgba(0, 119, 182, 0.6)', // Blue
                                borderColor: 'rgba(0, 119, 182, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Duration (seconds)',
                                data: durations,
                                backgroundColor: 'rgba(255, 87, 51, 0.6)', // Orange
                                borderColor: 'rgba(255, 87, 51, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }

        });
    </script>
@endsection
