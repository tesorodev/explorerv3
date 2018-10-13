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
        $.when(
            renderInitialBlocks()
        ).then(function () {
            setTimeout(function () {
                $.when(
                    displayDiffChart()
                ).then(function () {
                    setTimeout(function () {
                        refreshChart();
                    }, 100)
                });
            }, 500)
        });
    },
    update: function () {
        renderLastBlock();
        updateText('networkHeight', lastStats.height.toString());
        updateText('networkTransactions', lastStats.tx_count.toString());
        updateText('networkHashrate', getReadableHashRateString(lastStats.difficulty / blockTargetInterval));
        updateText('networkDifficulty', getReadableDifficultyString(lastStats.difficulty, 2).toString());
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
        url: '/api/getlastblockheader.php',
        method: "GET",
        dataType: 'json',
        cache: 'false',
        success: function (data) {
            lastBlockReward = data.result.block_header.reward;
            $.ajax({
                url: '/api/getgeneratedcoins.php',
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
function loadMoreBlocks_Click()
{
    if (xhrGetBlocks) xhrGetBlocks.abort();
    height = $('#blocks_rows').children().last().data('height')
    xhrGetBlocks = $.ajax({
        url: '/api/getblockheadersrange.php?start=' + (height - pageDisplay) + '&end=' + height,
        dataType: 'json',
        cache: 'false',
        success: function (data) {
            $.when(
                renderBlocks(data.result.headers)
            ).then(function () {
                setTimeout(function () {
                    loadMoreChart();
                }, 100)
            });
        }
    });
}

function prevPage_Click(e)
{
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
        url: '/api/getblockheadersrange.php?start=' + startHeight + '&end=' + endHeight,
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

function nextPage_Click(e)
{
    refresh = false;
    if (xhrGetBlocks) xhrGetBlocks.abort();
    var startHeight = $('#blocks_rows').children().first().data('height') + 1;
    var endHeight = startHeight + pageDisplay;

    if (endHeight > lastStats.height - 1)
    {
        endHeight = lastStats.height - 1;
        startHeight = endHeight - pageDisplay;
    }

    if (endHeight >= lastStats.height - 1)
        $('#next-page').addClass('disabled');
    else
        $('#next-page').removeClass('disabled');

    $('#prev-page').removeClass('disabled');

    xhrGetBlocks = $.ajax({
        url: '/api/getblockheadersrange.php?start=' + startHeight + '&end=' + endHeight,
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
    var height = document.getElementById('goto-height').value;
    var newUrl = "/?height=" + height;
    window.location = newUrl;
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

    if (endHeight > lastStats.height - 1)
    {
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
        url: '/api/getblockheadersrange.php?start=' + startHeight + '&end=' + endHeight,
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
        x: block.heigh,
        y: (block.difficulty / blockTargetInterval) / 1000.0
    });

    row.innerHTML = columns;

    return row;
}

function renderBlocks(blocksResults)
{
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
            for (var f = 0; f < rows.length; f++) {
                var bHeight = parseInt(rows[f].getAttribute('data-height'));
                if (bHeight < block.height) {
                    inserted = true;
                    $(rows[f]).before(blockElement);
                    break;
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

function calcAvgHashRate()
{
    if (!Difficulties)
        return;

    var sum = Difficulties.reduce(add, 0);
    function add(a, b) {
        return a + b;
    }
    var avgDiff = Math.round(sum / Difficulties.length);
    var avgHashRate = avgDiff / blockTargetInterval;

    updateText('avgDifficulty', getReadableDifficultyString(avgDiff, 2).toString());
    updateText('avgHashrate', getReadableHashRateString(avgDiff / blockTargetInterval));
    updateText('blockSolveTime', getReadableTime(lastStats.difficulty / avgHashRate));
}

function calcAvgSolveTime()
{
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

function getPoolTransactions()
{
    var xhrGetPool = $.ajax({
        url: '/api/gettransactionpool.php',
        cache: 'false',
        success: function (newdata) {
            if (typeof newdata.result !== "undefined") {
                var data = newdata.result.mempool;
                var totalAmount = 0;
                var totalFee = 0;
                var totalSize = 0;
                var txcount = 0;
                var txsRows = document.getElementById('mem_pool_rows');
                if (txsRows) {
                    while (txsRows.firstChild) {
                        txsRows.removeChild(txsRows.firstChild);
                    }
                }
                for (var i = 0; i < data.length; i++) {
                    var tx = data[i];

                    var row = document.createElement('tr');
                    var columns =
                        '<td>' + formatDate(tx.receiveTime) + ' (<span class="mtx-ago"></span>)' + '</td>' +
                        '<td>' + getReadableCoins(tx.amount_out, 4, true) + '</td>' +
                        '<td>' + getReadableCoins(tx.fee, 4, true) + '</td>' +
                        '<td>' + formatBytes(parseInt(tx.size)) + '</td>' +
                        '<td>' + formatPaymentLink(tx.hash) + '</td>';
                    row.innerHTML = columns;
                    $(txsRows).append(row);
                    $(row).find('.mtx-ago').timeago('update', new Date(tx.receiveTime * 1000).toISOString());

                    txcount = txcount + 1;
                    totalAmount = tx.amount_out + totalAmount;
                    totalFee = totalFee + tx.fee;
                    totalSize = totalSize + tx.size;

                }
                updateText('mempool_count', txcount);
                updateText('mempool_amount', getReadableCoins(totalAmount, 4));
                updateText('mempool_fees', getReadableCoins(totalFee, 8));
                updateText('mempool_sizes', formatBytes(parseInt(totalSize)));
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
            label: "Difficulty",
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

function getReadableTime(seconds)
{
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