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
        <h4><i class="fa fa-cube fa-fw"></i> Block <small id="block.hash" style="word-break: break-all;"></small></h4>
        <div class="row">
            <div class="col-sm-6 col-md-6">
                <ul class="list-group" style="margin-bottom: 0px;">

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-bars"></i> Height: <span class="value" id="block_height"><span id="block.height"></span></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-clock-o"></i> Timestamp: <span class="value" id="block.timestamp"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-code-fork"></i> Version: <span class="value" id="block.version"></span>
					</li>
					
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-unlock-alt"></i> Difficulty: <span class="value" id="block.difficulty"></span>
					</li>
					
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-exchange"></i> Transactions: <span class="value" id="block.transactions"></span>
                    </li>

                </ul>
            </div>
            <div class="col-sm-6 col-md-6">
                <ul class="list-group" style="margin-bottom: 0px;">

					<li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-bars"></i> Depth: <span class="value" id="block.depth"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-expand"></i> Block Size: <span class="value" id="block.blockSize"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-key"></i> Nonce: <span class="value" id="block.nonce"></span>
					</li>
					
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-exchange"></i> Miner TX: <span class="value" id="block.minerTxHash"></span>
					</li>
					
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-money"></i> Reward: <span class="value" id="block.reward"></span>
                    </li>

                </ul>
            </div>
        </div>
    </form>
</div>

<div class="well bs-component">
    <form class="form-horizontal">
        <h4><i class="fa fa-exchange fa-fw"></i> Transactions</h4>
        <div class="row">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="mem_pool_table">
                        <thead>
                            <tr>
                                <th><i class="fa fa-paw"></i> Hash</th>
                            </tr>
                        </thead>
                        <tbody id="transactions_rows"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    var block, xhrGetBlock, xhrGetBlock2;

    currentPage = {
        destroy: function(){
			if (xhrGetBlock) xhrGetBlock.abort();
			if (xhrGetBlock2) xhrGetBlock2.abort();
        },
        init: function(){
            getBlock();
        },
        update: function(){
        }
    };

    function getBlock(){
		if (xhrGetBlock) xhrGetBlock.abort();
		if (xhrGetBlock2) xhrGetBlock2.abort();
		var searchBlk = $.parseJSON(sessionStorage.getItem('searchBlock'));
		if (searchBlk) {
			renderBlock(searchBlk);
		} else {
			xhrGetBlock = $.ajax({
				url: './api/daemon/get_block/?hash=' + urlParam('hash'),
				dataType: 'json',
				cache: 'false',
				success: function(data){
					block = data.result;
					renderBlock(block);
				}
			});
		}
		sessionStorage.removeItem('searchBlock');
    }

	function renderBlock(data)
	{
		updateText('block.hash', data.block_header.hash);
		updateTextLinkable('block.minerTxHash', formatShortPaymentLink(data.miner_tx_hash));
		updateText('block.height', data.block_header.height);
		updateText('block.depth', data.block_header.depth);
		updateText('block.timestamp', formatDate(data.block_header.timestamp));
		updateText('block.version', data.block_header.major_version);
		updateText('block.difficulty', data.block_header.difficulty);
		updateText('block.transactions', data.block_header.num_txes);
		updateText('block.blockSize', formatBytes(parseInt(data.block_header.block_size)));
		updateText('block.nonce', data.block_header.nonce);
		updateText('block.reward', getReadableCoins(data.block_header.reward, 4));

		var hashes = 'hash[]=' + data.miner_tx_hash;

		for (var i = 0; i < data.block_header.num_txes; i++)
			hashes += '&hash[]=' + data.tx_hashes[i];

		$.ajax({
			url: './api/daemon/get_transactions/?' + hashes,
			dataType: 'json',
			cache: 'false',
			success: function(data2){
				renderTransactions(data2.result);
			},
			error: function (ajaxContext) {
			}
		});

		makePrevBlockLink(data.block_header.prev_hash);

		$.ajax({
			url: './api/daemon/get_block_header_by_height/?height=' + (data.block_header.height + 1),
			dataType: 'json',
			cache: 'false',
			success: function(data3){
				makeNextBlockLink(data3.result.block_header.hash);
			},
			error: function (ajaxContext) {
			}
		});

	}

    function getTransactionCells(transaction){
        return '<td>' + formatPaymentLink(transaction.tx_hash) + '</td>';
    }

    function getTransactionRowElement(transaction, jsonString){

        var row = document.createElement('tr');
        row.setAttribute('data-json', jsonString);
        row.setAttribute('data-hash', transaction.tx_hash);
        row.setAttribute('id', 'transactionRow' + transaction.tx_hash);

        row.innerHTML = getTransactionCells(transaction);

        return row;
    }

    function renderTransactions(transactionResults){

		var $transactionsRows = $('#transactions_rows');

		for (var i = 0; i < transactionResults.length; i++)
		{
			var transaction = transactionResults[i];
            var transactionJson = JSON.stringify(transaction);
            var existingRow = document.getElementById('transactionRow' + transaction.tx_hash);
            if (existingRow && existingRow.getAttribute('data-json') !== transactionJson)
                $(existingRow).replaceWith(getTransactionRowElement(transaction, transactionJson));
            else if (!existingRow)
                $transactionsRows.append(getTransactionRowElement(transaction, transactionJson));
        }
    }

	function makeNextBlockLink(blockHash){
		$('#block_height').append(' <a href="' + getBlockchainUrl(blockHash) + '" title="Next block"><i class="fa fa-chevron-circle-right"></i></a>');
	}

	function makePrevBlockLink(blockHash){
		$('#block_height').prepend('<a href="' + getBlockchainUrl(blockHash) + '" title="Previous block"><i class="fa fa-chevron-circle-left"></i></a> ');
	}

	function formatPrevNextBlockLink(hash){
        return '<a href="' + getBlockchainUrl(hash) + '">' + hash + '</a>';
    }

	$(function() {
		$('[data-toggle="tooltip"]').tooltip();
	});
//# sourceURL=./pages/blockchain_block.php
</script>
