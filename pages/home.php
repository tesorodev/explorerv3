<style>
    .value {
        color: #2780E3;
        font-weight: 600;
        display: block;
        float: right;
    }
    h4 {
        color:#434343;
    }
</style>
<div class="well bs-component">
    <form class="form-horizontal">
        <h4><i class="fa fa-info fa-fw"></i> Node Status</h4>
        <div class="row">
            <div class="col-sm-6 col-md-3">
                <ul class="list-group" style="margin-bottom: 0px;">

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-list-ol"></i> Height: <span id="networkHeight" class="value"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-exchange"></i> Transactions: <span id="networkTransactions"
                            class="value"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-certificate"></i> Reward: <span id="currentReward" class="value"></span>
                    </li>

                </ul>
            </div>
            <div class="col-sm-6 col-md-3">
                <ul class="list-group" style="margin-bottom: 0px;">

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-money"></i> Supply: <span id="totalCoins" class="value"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-university"></i> Emission: <span id="emissionTotal" class="value"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-university"></i> Emission %: <span id="emissionPercent" class="value"></span>
                    </li>

                </ul>
            </div>
            <div class="col-sm-6 col-md-3">
                <ul class="list-group" style="margin-bottom: 0px;">

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-unlock-alt"></i> Next Difficulty: <span id="networkDifficulty"
                            class="value"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-lock"></i> Average Difficulty: <span id="avgDifficulty" class="value"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-tachometer"></i> Hash Rate: <span id="networkHashrate" class="value"></span>
                    </li>

                </ul>
            </div>
            <div class="col-sm-6 col-md-3">
                <ul class="list-group" style="margin-bottom: 0px;">

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-tachometer"></i> Avg. Hash Rate: <span id="avgHashrate" class="value"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-clock-o" aria-hidden="true"></i> Est. solve time: <span id="blockSolveTime"
                            class="value"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-clock-o" aria-hidden="true"></i> Avg. solve time: <span id="avgSolveTime"
                            class="value"></span>
                    </li>

                </ul>
            </div>
        </div>
    </form>
</div>

<div class="well bs-component">
    <form class="form-horizontal">
        <h4><i class="fa fa-bar-chart fa-fw"></i> Network Hashrate</h4>
        <div class="row">
            <div class="panel-body">
                <canvas id="difficultyChart" height="210"></canvas>
            </div>
        </div>
    </form>
</div>

<div class="well bs-component">
    <form class="form-horizontal">
        <h4><i class="fa fa-exchange fa-fw"></i> Transaction Pool</h4>
        <div class="row">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="mem_pool_table">
                        <thead>
                            <tr>
                                <th width="30%"><i class="fa fa-clock-o"></i> Date &amp; Time</th>
                                <th width="40%"><i class="fa fa-paw"></i> Hash</th>
                            </tr>
                        </thead>
                        <tbody id="mem_pool_rows"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="well bs-component">
    <form class="form-horizontal">
        <h4><i class="fa fa-chain fa-fw"></i> Recent blocks</h4>
        <div class="row">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <a id="prev-page" href="#" class="btn btn-default input-group-addon"><i class="fa fa-arrow-left"></i> Older</a>
                            <input id="goto-height" type="text" class="form-control" placeholder="Height">
                            <a id="goto-height-go" href="#" class="btn btn-default input-group-addon">Go</a>
                            <a id="next-page" href="#" class="btn btn-default input-group-addon disabled">Newer <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="input-group">
                            <input class="form-control" placeholder="Search by block height, block hash or transaction hash" id="txt_search">
                            <span class="input-group-btn"><button class="btn btn-default" type="button" id="btn_search">Search</button></span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fa fa-bars"></i> Height</th>
                                <th><i class="fa fa-clock-o"></i> Date &amp; time</th>
                                <th><i class="fa fa-archive"></i> Size</th>
                                <th><i class="fa fa-paw"></i> Block Hash</th>
                                <th><i class="fa fa-unlock-alt"></i> Difficulty</th>
                                <th><i class="fa fa-exchange"></i> Txs</th>
                            </tr>
                        </thead>
                        <tbody id="blocks_rows">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    var block,
        chartDataSetDiff = [],
        Difficulties = [],
        Blocks = [],
        time_stamps = [],
        diffChart,
        refresh = true,
        options;

    currentPage = {
        destroy: function () {
            if (xhrGetBlocks) xhrGetBlocks.abort();
        },
        init: function () {
            setTimeout(function () {
                $.when(
                    displayDiffChart()
                ).then(function () {
                    setTimeout(function () {
                        refreshChart();
                    }, 100)
                });
            }, 500)
        },
        update: function () {
            renderLastBlock();
            updateText('networkHeight', lastStats.height);
            updateText('networkTransactions', lastStats.tx_count);
            updateText('networkHashrate', getReadableHashRateString(lastStats.difficulty / blockTargetInterval));
            updateText('networkDifficulty', getReadableDifficultyString(lastStats.difficulty, 2));
            $("time.timeago").timeago();
            getPoolTransactions();
            var currHeight = $('#blocks_rows').children().first().data('height');
            if (refresh) {
                $.when(
                    renderInitialBlocks()
                ).then(function () {
                    setTimeout(function () {
                        refreshChart();
                    }, 100)
                });
            }
            if ((currHeight + 31) < lastStats.height) {
                $('#next-page').removeClass('disabled');
            }
        }
    };

    function renderLastBlock() {
        $.ajax({
            url: './api/daemon/get_last_block_header/',
            method: "GET",
            dataType: 'json',
            cache: 'false',
            success: function (data) {
                lastBlockReward = data.result.block_header.reward;
                $.ajax({
                    url: './api/daemon/get_generated_coins/',
                    dataType: 'json',
                    cache: 'false',
                    success: function (data) {
                        generatedCoins = data.result.coins;
                        updateText('totalCoins', getReadableCoins(coinTotal, 2));
                        updateText('emissionTotal', getReadableCoins(generatedCoins, 2));
                        updateText('emissionPercent', (generatedCoins / coinTotal * 100).toFixed(2) + "%");
                        updateText('currentReward', getReadableCoins(lastBlockReward, 4));
                    }
                });
            }
        });
    }

    var xhrGetBlocks;

    function prevPage_Click(e) {
        refresh = false;
        if (xhrGetBlocks) xhrGetBlocks.abort();

        var endHeight = $('#blocks_rows').children().last().data('height') - 1;
        var startHeight = endHeight - pageDisplay;

        if (startHeight < 0)
            startHeight = 0;

        if (startHeight <= 0)
            $('#prev-page').addClass('disabled');
        else
            $('#prev-page').removeClass('disabled');

        $('#next-page').removeClass('disabled');

        xhrGetBlocks = $.ajax({
            url: './api/daemon/get_block_headers_range/?start=' + startHeight + '&end=' + endHeight,
            dataType: 'json',
            cache: 'false',
            success: function (data) {
                $('#blocks_rows').children().remove();
                $.when(
                    renderBlocks(data.result.headers)
                ).then(function () {
                    setTimeout(function () {
                        refreshChart();
                    }, 100)
                });
            }
        });
        e.preventDefault();
    }

    function nextPage_Click(e) {
        refresh = false;
        if (xhrGetBlocks) xhrGetBlocks.abort();
        var startHeight = $('#blocks_rows').children().first().data('height') + 1;
        var endHeight = startHeight + pageDisplay;

        if (endHeight > lastStats.height - 1) {
            endHeight = lastStats.height - 1;
            startHeight = endHeight - pageDisplay;
        }

        if (endHeight >= lastStats.height - 1)
            $('#next-page').addClass('disabled');
        else
            $('#next-page').removeClass('disabled');

        $('#prev-page').removeClass('disabled');

        xhrGetBlocks = $.ajax({
            url: './api/daemon/get_block_headers_range/?start=' + startHeight + '&end=' + endHeight,
            dataType: 'json',
            cache: 'false',
            success: function (data) {
                $('#blocks_rows').children().remove();
                $.when(
                    renderBlocks(data.result.headers)
                ).then(function () {
                    setTimeout(function () {
                        refreshChart();
                    }, 100)
                });
            }
        });
        e.preventDefault();
    };

    function gotoHeightGo_Click() {

        var endHeight = document.getElementById('goto-height').value;
        var startHeight = endHeight - pageDisplay;

        xhrGetBlocks = $.ajax({
            url: './api/daemon/get_block_headers_range/?start=' + startHeight + '&end=' + endHeight,
            dataType: 'json',
            cache: 'false',
            success: function (data) {
                $('#blocks_rows').children().remove();
                $.when(
                    renderBlocks(data.result.headers)
                ).then(function () {
                    setTimeout(function () {
                        refreshChart();
                    }, 100)
                });
            }
        });
        e.preventDefault();
    };

    function renderInitialBlocks() {
        if (xhrGetBlocks) xhrGetBlocks.abort();

        var loadHeight;

        if (urlParam('height'))
            loadHeight = parseInt(urlParam('height'));
        else
            loadHeight = lastStats.height - 1;

        var endHeight = loadHeight;
        var startHeight = endHeight - pageDisplay;

        if (endHeight > lastStats.height - 1) {
            endHeight = lastStats.height - 1;
            startHeight = endHeight - pageDisplay;
        }

        if (startHeight < 0)
            startHeight = 0;

        if (startHeight <= 0)
            $('#prev-page').addClass('disabled');
        else
            $('#prev-page').removeClass('disabled');

        if (endHeight >= lastStats.height - 1)
            $('#next-page').addClass('disabled');
        else
            $('#next-page').removeClass('disabled');

        xhrGetBlocks = $.ajax({
            url: './api/daemon/get_block_headers_range/?start=' + startHeight + '&end=' + endHeight,
            dataType: 'json',
            cache: 'false',
            success: function (data) {
                renderBlocks(data.result.headers);
            }
        });
    };

    function getBlockRowElement(block, jsonString) {

        var row = document.createElement('tr');
        row.setAttribute('data-json', jsonString);
        row.setAttribute('data-height', block.height);
        row.setAttribute('id', 'blockRow' + block.height);
        row.setAttribute('title', block.hash);
        var dateTime = new Date(block.timestamp * 1000).toISOString();
        row.setAttribute('data-dt', dateTime);
        var columns =
            '<td>' + block.height + '</td>' +
            '<td class="date-time">' + formatDate(block.timestamp) + ' (<time class="timeago" datetime="' + dateTime + '"></time>)</td>' +
            '<td>' + formatBytes(parseInt(block.block_size)) + '</td>' +
            '<td>' + formatBlockLink(block.hash) + '</td>' +
            '<td class="blk-diff">' + block.difficulty + '</td>' +
            '<td>' + block.num_txes + '</td>';

        Difficulties.push(parseInt(block.difficulty));
        time_stamps.push(block.timestamp);

        chartDataSetDiff.push({
            x: block.height,
            y: (block.difficulty / blockTargetInterval) / 1000.0
        });

        var $blocksRows = $('#blocks_rows');

        if ($blocksRows.children().length >= 30) {
            var prune = chartDataSetDiff.length - $blocksRows.children().length;

            Difficulties.splice(0, prune);
            time_stamps.splice(0, prune);
            chartDataSetDiff.splice(0, prune);
        }


        row.innerHTML = columns;

        return row;
    }

    function renderBlocks(blocksResults) {
        if (!blocksResults)
            return;

        var $blocksRows = $('#blocks_rows');

        for (var i = 0; i < blocksResults.length; i++) {

            var block = blocksResults[i];

            var blockJson = JSON.stringify(block);

            var existingRow = document.getElementById('blockRow' + block.height);

            if (existingRow && existingRow.getAttribute('data-json') !== blockJson) {
                $(existingRow).replaceWith(getBlockRowElement(block, blockJson));
            }
            else if (!existingRow) {

                var blockElement = getBlockRowElement(block, blockJson);

                var inserted = false;
                var rows = $blocksRows.children().get();
                var added = 0;
                for (var f = 0; f < rows.length; f++) {
                    var bHeight = parseInt(rows[f].getAttribute('data-height'));
                    if (bHeight < block.height) {
                        inserted = true;
                        $(rows[f]).before(blockElement);
                        added++;
                        break;
                    }
                }
                if (rows.length >= 30 + added) {
                    for (var g = 0; g < added; g++) {
                        $blocksRows.children().last().remove();
                    }
                }
                if (!inserted) {
                    $blocksRows.append(blockElement);
                }
            }
        }
        $("time.timeago").timeago();
        calcAvgHashRate();
        calcAvgSolveTime();
    }

    function calcAvgHashRate() {
        if (!Difficulties)
            return;

        var sum = Difficulties.reduce(add, 0);
        function add(a, b) {
            return a + b;
        }
        var avgDiff = Math.round(sum / Difficulties.length);
        var avgHashRate = avgDiff / blockTargetInterval;

        updateText('avgDifficulty', getReadableDifficultyString(avgDiff, 2));
        updateText('avgHashrate', getReadableHashRateString(avgDiff / blockTargetInterval));
        updateText('blockSolveTime', getReadableTime(lastStats.difficulty / avgHashRate));
    }

    function calcAvgSolveTime() {
        if (!time_stamps)
            return;

        ts = time_stamps.concat([]);
        ts.sort();
        var avg_solve_time = 0;
        var solveTime = 0;
        for (var i = 1; i < ts.length; i++) {
            solveTime += ts[i] - ts[i - 1];
        }
        avg_solve_time = solveTime / (ts.length - 1);
        updateText('avgSolveTime', getReadableTime(avg_solve_time));
    }

    function getPoolTransactions() {
        var xhrGetPool = $.ajax({
            url: './api/daemon/get_transaction_pool/',
            cache: 'false',
            success: function (data) {
                var json = data;
                var transactions = json.transactions;

                if (transactions) {
                    transactions.sort(function (a, b) { return a.receive_time - b.receive_time });

                    var txsRows = document.getElementById('mem_pool_rows');
                    if (txsRows)
                        while (txsRows.firstChild)
                            txsRows.removeChild(txsRows.firstChild);

                    for (var i = 0; i < transactions.length; i++) {
                        var tx = transactions[i];

                        var row = document.createElement('tr');
                        var columns =
                            '<td>' + formatDate(tx.receive_time) + ' (<span class="mtx-ago"></span>)' + '</td>' +
                            '<td>' + formatPaymentLink(tx.id_hash) + '</td>';
                        row.innerHTML = columns;
                        $(txsRows).append(row);
                        $(row).find('.mtx-ago').timeago('update', new Date(tx.receive_time * 1000).toISOString());
                    }
                }
            }
        });
    }

    // Difficulty chart
    function displayDiffChart() {
        var ctx = document.getElementById("difficultyChart");
        var chartData = {
            datasets: [{
                data: chartDataSetDiff,
                yAxisID: "diff",
                label: "Hashrate",
                backgroundColor: "rgba(220,220,220,0.2)",
                borderColor: '#2FA4E7',
                borderWidth: 2,
                pointColor: "#2FA4E7",
                pointBorderColor: "#2FA4E7",
                pointHighlightFill: "#2FA4E7",
                pointBackgroundColor: "#2FA4E7",
                pointBorderWidth: 2,
                pointRadius: 1,
                pointHoverRadius: 3,
                pointHitRadius: 20,
                type: 'line'
            }]
        };
        var options = {
            responsive: true,
            maintainAspectRatio: false,
            elements: {
                line: {
                    tension: 0
                }
            },
            title: {
                display: false
            },
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    type: 'linear',
                    id: "diff",
                    distribution: 'linear',
                    position: 'left',
                    scaleLabel: {
                        display: true,
                        labelString: 'NetHash (Kh/s)'
                    },
                    gridLines: {
                        lineWidth: 1,
                        display: true
                    },
                    ticks: {
                        fontSize: 9,
                        display: true
                    },
                    display: true
                }],
                xAxes: [
                    {
                        type: 'linear',
                        distribution: 'linear',
                        position: 'left',
                        scaleLabel: {
                            display: false,
                            labelString: 'Height'
                        },
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            fontSize: 9,
                            display: false,
                            stepSize: 1
                        },
                        display: true
                    }]
            }
        };

        if (!chartData || !options)
            return;

        diffChart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: options
        });
    }

    function refreshChart() {
        var brows = $('#blocks_rows').children(),
            chartDataSetDiff = [];

        if (!brows)
            return;

        for (var i = 0; i < brows.length; i++) {
            var row = $(brows[i]);
            chartDataSetDiff.push({
                x: parseInt(row.data("json").height),
                y: (parseInt(row.data("json").difficulty) / blockTargetInterval) / 1000.0
            });
        }

        if (diffChart) {
            diffChart.data.datasets[0].data = chartDataSetDiff.reverse();
            diffChart.update();
        }
    }

    function loadMoreChart() {
        refreshChart();
    }

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    function getReadableTime(seconds) {
        var units = [[60, 's.'], [60, 'min.'], [24, 'h.'],
        [7, 'd.'], [4, 'w.'], [12, 'м.'], [1, 'y.']];

        function formatAmounts(amount, unit) {
            var rounded = Math.round(amount);
            return '' + rounded + ' ' + unit + (rounded > 1 ? '' : '');
        }

        var amount = seconds;
        for (var i = 0; i < units.length; i++) {
            if (amount < units[i][0])
                return formatAmounts(amount, units[i][1]);
            amount = amount / units[i][0];
        }
        return formatAmounts(amount, units[units.length - 1][1]);
    }

    $('#prev-page').click(function (e) {
        prevPage_Click(e);
    });

    $('#next-page').click(function (e) {
        nextPage_Click(e);
    });

    $('#goto-height-go').click(function () {
        gotoHeightGo_Click();
    });

    $('#goto-height').keyup(function (e) {
        if (e.keyCode === 13)
            gotoHeightGo_Click();
    });
//# sourceURL=./pages/home.php
</script>

<script>
    $('#btn_search').click(function (e) {

        var text = document.getElementById('txt_search').value;

        function GetSearchBlockbyHeight() {

            var block, xhrGetSearchBlockbyHeight;

            if (xhrGetSearchBlockbyHeight) xhrGetSearchBlockbyHeight.abort();

            xhrGetSearchBlockbyHeight = $.ajax({
                url: './api/daemon/get_block_header_by_height/?height=' + text,
                dataType: 'json',
                cache: 'false',
                success: function (data) {
                    if (data.result) {
                        block = data.result.block_header;
                        window.location.href = getBlockchainUrl(block.hash);
                    }
                }
            });
        }

        function GetSearchBlock() {
            var block, xhrGetSearchBlock;

            if (xhrGetSearchBlock) xhrGetSearchBlock.abort();

            xhrGetSearchBlock = $.ajax({
                url: './api/daemon/get_block/?hash=' + text,
                dataType: 'json',
                cache: 'false',
                success: function (data) {
                    block = data.result;
                    sessionStorage.setItem('searchBlock', JSON.stringify(block));
                    window.location.href = getBlockchainUrl(block.hash);
                },
                error: function (err) {
                    $.ajax({
                        url: './api/daemon/get_transactions/?hash[]=' + text,
                        dataType: 'json',
                        cache: 'false',
                        success: function (data2) {
                            sessionStorage.setItem('searchTransaction', JSON.stringify(data2.result));
                            window.location.href = transactionExplorer.replace('{id}', text);
                        },
                        error: function (err2) {
                            alertError("Search failed");
                        }
                    });
                }
            });
        }

        if (text.length < 64) {
            GetSearchBlockbyHeight();
        } else if (text.length == 64) {
            GetSearchBlock();
        } else {
            alertError("Invalid search term");
        }

        e.preventDefault();

    });

    function alertError(message) {
        $('#page').after(
            '<div class="alert alert-danger alert-dismissable fade in" style="position: fixed; right: 50px; top: 50px;">' +
            '<button id="popup_alert" type="button" class="close" ' +
            'data-dismiss="alert" aria-hidden="true">' +
            '&times;' +
            '</button>' +
            '<strong>' + message + '</strong><br />' +
            '</div>');
    }

    $('#txt_search').keyup(function (e) {
        if (e.keyCode === 13)
            $('#btn_search').click();
    });
</script>
