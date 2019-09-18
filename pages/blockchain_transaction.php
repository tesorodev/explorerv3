<style>
    .value {
        color: #2780E3;
        font-weight: 600;
        display: block;
        float: right;
    }
    .value-warning {
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
        <h4><i class="fa fa-cube fa-fw"></i> TX <small id="transaction.hash" style="word-break: break-all;"></small></h4>
        <div class="row">
            <div class="col-sm-6 col-md-6">
                <ul class="list-group" style="margin-bottom: 0px;">

                    <div id="tx_confirm_count" style="display: none;">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <i class="fa fa-bars"></i> Confirms: <span class="value" id="transaction.confirmations"></span>
                        </li>
                    </div>

                    <div id="tx_unconfirmed">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <i class="fa fa-bars"></i> Confirms: <span class="value-warning text-warning">Unconfirmed</span>
                        </li>
                    </div>

                    <div id="amt_hidden">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <i class="fa fa-code-fork"></i> Sum:  <span class="value-warning text-warning">Hidden</span>
                        </li>
                    </div>
                    
                    <div id="amt_visible">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <i class="fa fa-code-fork"></i> Sum: <span class="value" id="transaction.amount_out"></span>
                        </li>
                    </div>
                    
                    <div id="fee_visible">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <i class="fa fa-unlock-alt"></i> Fee: <span class="value" id="transaction.fee"></span>
                        </li>
                    </div>

                    <div id="fee_hidden">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <i class="fa fa-unlock-alt"></i> Fee: <span class="value-warning text-warning">None</span>
                        </li>
                    </div>
					
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-exchange"></i> Type: <span class="value" id="transaction.type"></span>
                    </li>

                </ul>
            </div>
            <div class="col-sm-6 col-md-6">
                <ul class="list-group" style="margin-bottom: 0px;">

					<li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-circle-o"></i> Block Hash: <span class="value" id="block.hash"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-circle-o"></i> Height: <span class="value" id="block.height"></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-circle-o"></i> Timestamp: <span class="value" id="block.timestamp"></span>
                    </li>
                    
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <i class="fa fa-circle-o"></i> TX Count: <span class="value" id="block.num_txes"></span>
					</li>

                </ul>
            </div>
        </div>
    </form>
</div>

<div class="well bs-component">
    <form class="form-horizontal">
        <h4><i class="fa fa-arrow-circle-right fa-fw"></i> Inputs (<span id="inputs_count"></span>)</h4>
        <div class="row">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="mem_pool_table">
                        <thead>
                            <tr>
                                <th><i class="fa fa-money"></i> Amount</th>
                                <th><i class="fa fa-paw"></i> Image</th>
                                <th><i class="fa fa-arrows-alt"></i> Offset</th>
                            </tr>
                        </thead>
                        <tbody id="inputs_rows"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="well bs-component">
    <form class="form-horizontal">
        <h4><i class="fa fa-arrow-circle-left fa-fw"></i> Outputs (<span id="outputs_count"></span>)</h4>
        <div class="row">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="mem_pool_table">
                        <thead>
                            <tr>
                                <th><i class="fa fa-money"></i> Amount</th>
                                <th><i class="fa fa-key"></i> Key</th>
                            </tr>
                        </thead>
                        <tbody id="outputs_rows"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    var xhrGetTransaction, transaction;

    currentPage = {
        destroy: function(){
            if (xhrGetTransaction) xhrGetTransaction.abort();
        },
        init: function(){
            getTransaction();
        },
        update: function(){
        }
    };

    function getTransaction(){
        if (xhrGetTransaction) xhrGetTransaction.abort();
		var searchTx = $.parseJSON(sessionStorage.getItem('searchTransaction'));
		if (searchTx) {
            getTransactionData(searchTx[0], searchTx[0].tx_hash);
		} else {
			xhrGetTransaction = $.ajax({
				url: './api/daemon/get_transactions/?hash[]=' + urlParam('hash'),
				dataType: 'json',
				cache: 'false',
				success: function(data){
                    var tx = data.result[0];
                    getTransactionData(tx);
				}
            });
		}
		sessionStorage.removeItem('searchTransaction');
    }

    function getTransactionData(tx)
    {
        var txExtra = "";
        for (var i = 0; i < tx.json.extra.length; i++)
            txExtra += tx.json.extra[i].toString(16).padStart(2, '0');

        $.ajax({
            url: './api/daemon/get_tx_pubkey/?extra=' + txExtra,
            dataType: 'json',
            cache: 'false',
            success: function(data3){
                renderTransaction(txExtra, data3.result.pubkey, tx);
                $.ajax({
                    url: './api/daemon/get_block_header_by_height/?height=' + tx.block_height,
                    dataType: 'json',
                    cache: 'false',
                    success: function(data2) {
                        renderTransactionBlockData(data2.result.block_header);
                    }
                });
            }
        });
    }

    function renderTransaction(txExtra, txPubKey, transaction)
    {
        if (transaction.in_pool)
        {
            $('#tx_block').hide();
			$('#tx_unconfirmed').show();
            $('#tx_confirm_count').hide();
        }
        else
        {
            $('#tx_block').show();
			$('#tx_unconfirmed').hide();
            $('#tx_confirm_count').show();
        }

        updateText('transaction.timestamp', formatDate(transaction.timestamp));

        if (transaction.json.rct_signatures.txnFee) {
            updateText('transaction.fee', getReadableCoins(transaction.json.rct_signatures.txnFee, 6));
            $('#fee_hidden').hide();
            $('#fee_visible').show();
        } else {
            $('#fee_hidden').show();
            $('#fee_visible').hide();
        }

        var outputTotal = 0;
        for (var i = 0; i < transaction.json.vout.length; i++)
            outputTotal += transaction.json.vout[i].amount;

        if (outputTotal == 0) {
            $('#amt_hidden').show();
            $('#amt_visible').hide();
        } else {
            $('#amt_hidden').hide();
            $('#amt_visible').show();
        }

        updateText('transaction.amount_out', getReadableCoins(outputTotal, 6));
        updateText('transaction.type', getTransactionType(transaction.json.rct_signatures.type));
        updateText('transaction.extra', txExtra);
        updateText('transaction.pubkey', txPubKey);
        updateText('transaction.hash', transaction.tx_hash);

        renderInputs(transaction.json.vin);
        renderOutputs(transaction.json.vout);
	}

    function renderTransactionBlockData(block)
    {
        updateText('transaction.confirmations', lastStats.height - block.height);
        updateTextLinkable('block.hash', formatShortBlockLink(block.hash));
        updateText('block.height', block.height);
        updateText('block.timestamp', formatDate(block.timestamp));
        updateText('block.num_txes', block.num_txes)
	}

    function getInputCells(input){
        return '<td>' + getReadableCoins(input.key.amount) + '</td>' +
            '<td>' + input.key.k_image + '</td>' +
            '<td>' + input.key.key_offsets + '</td>';
    }


    function getInputRowElement(input, jsonString){

        var row = document.createElement('tr');
        row.setAttribute('data-json', jsonString);
        row.setAttribute('data-k_image', input.key.k_image);
		row.setAttribute('data-key_offsets', input.key.key_offsets);
        row.setAttribute('id', 'inputRow' + input.key.k_image);

        row.innerHTML = getInputCells(input);

        return row;
    }

    function renderInputs(inputResults){

        var $inputsRows = $('#inputs_rows');

        for (var i = 0; i < inputResults.length; i++){

            var input = inputResults[i];

            if (!input.key)
                continue;

            var inputJson = JSON.stringify(input);

            var existingRow = document.getElementById('inputRow' + input.key.k_image);

            if (existingRow && existingRow.getAttribute('data-json') !== inputJson){
                $(existingRow).replaceWith(getInputRowElement(input, inputJson));
            }
            else if (!existingRow){

                var inputElement = getInputRowElement(input, inputJson);
                $inputsRows.append(inputElement);
            }

        }

		updateText('inputs_count', document.querySelectorAll('#inputs_rows tr').length);
    }


    function getOutputCells(output){
        return '<td>' + getReadableCoins(output.amount) + '</td>' +
            '<td>' + output.target.key + '</td>';
    }


    function getOutputRowElement(output, jsonString){

        var row = document.createElement('tr');
        row.setAttribute('data-json', jsonString);
        row.setAttribute('data-k_image', output.target.key);
        row.setAttribute('id', 'outputRow' + output.target.key);

        row.innerHTML = getOutputCells(output);

        return row;
    }

    function renderOutputs(outputResults){

        var $outputsRows = $('#outputs_rows');

        for (var i = 0; i < outputResults.length; i++){

            var output = outputResults[i];

            var outputJson = JSON.stringify(output);

            var existingRow = document.getElementById('outputRow' + output.target.key);

            if (existingRow && existingRow.getAttribute('data-json') !== outputJson){
                $(existingRow).replaceWith(getOutputRowElement(output, outputJson));
            }
            else if (!existingRow){

                var outputElement = getOutputRowElement(output, outputJson);
                $outputsRows.append(outputElement);
            }
        }

		updateText('outputs_count', document.querySelectorAll('#outputs_rows tr').length);
    }

	$(function() {
		$('[data-toggle="tooltip"]').tooltip();
    });
//# sourceURL=./pages/blockchain_transaction.php
</script>
