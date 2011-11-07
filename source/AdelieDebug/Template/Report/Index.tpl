<style>
.adelieDebug {
	background: #fff;
	text-align: left;
	}

.adelieDebug p.h1 {
	font-size: 30px;
	font-weight: bold;
	}

.adelieDebug p.h2 {
	font-size: 20px;
	font-weight: bold;
	}

.adelieDebug p.h3 {
	font-size: 16px;
	font-weight: bold;
	}

.adelieDebug p {
	font-size: 15px;
	}

.adelieDebug pre {
	border:none; 
	text-align:left; 
	font-size: 15px; 
	font-family: Consolas,monospace;
	padding: 2px; 
	margin: 1px 0; 
	overflow: auto;
	white-space: -moz-pre-wrap; /* Mozilla */
	white-space: -pre-wrap;     /* Opera 4-6 */
	white-space: -o-pre-wrap;   /* Opera 7 */
	white-space: pre-wrap;      /* CSS3 */
	word-wrap: break-word;      /* IE 5.5+ */
	}

.adelieDebug pre.console {
	background-color: #ECECEC;
	border: 1px solid #ccc;
	}

.adelieDebug pre.SQL,
.adelieDebug pre.PHP {
	cursor: pointer;
}

.adelieDebug pre.info {
	background: #DFF2BF;
	color: #4F8A10;
	}

.adelieDebug pre.OVERLAP {
	background: #FEEFB3;
	color: #9F6000;
	}

.adelieDebug pre.MARK {
	color: #00529B;
	background-color: #BDE5F8;
	}

.adelieDebug pre.ERROR {
	background: #FFBABA;
	color: #D8000C;
	}

.adelieDebug table.data {
	border: 1px solid #eee;
	border-collapse: collapse;
	font-family: Consolas,monospace;
	width: 100%;
	}

.adelieDebug table.data th {
	border-top: 1px solid #E2F4FA;
	border-right: none;
	border-bottom: 1px solid #E2F4FA;
	border-left: none;
	padding: 2px;
	background-color: #333;
	color: #fff;
	font-weight: normal;
	font-size: 15px; 
	text-align: left;
	}

.adelieDebug table.data td {
	border-top: 1px solid #fff;
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	border-left: 1px solid #fff;
	padding: 2px;
	font-size: 15px; 
	}
.adelieDebug table.data td:first-child {
	width: 200px;
	}

.adelieDebug table.data thead tr {
	border-top: 1px solid #E2F4FA;
	}

.adelieDebug table.data tbody tr {
	border-bottom: 1px solid #ccc;
	background-color: white;
	}

.adelieDebug table.data tbody tr:nth-child(even) {
	background-color: #ECECEC;
	}

.adelieDebug table.data tbody tr:hover {
	background-color: #ccc;
	}

.adelieDebug pre.expanded {
	height: auto;
	overflow-y: auto;
	cursor: pointer;
	}

.adelieDebug pre.shortened {
	height: 1em;
	overflow-y: hidden;
	cursor: pointer;
	}

.adelieDebug span.expandMore {
	float: left;
	}
</style>
<div class="adelieDebug">
	<p class="h1">Adelie Debug</p>
	<p class="h2">タイムライン</p>
	<div id="adelieDebugPhpErrors">
		<table class="data">
			<tr>
				<th>No.</th>
				<th>ms</th>
				<th>Type</th>
				<th>Message</th>
			</tr>
		<{foreach from=$logs item="log"}>
			<tr>
				<td style="width: 10px;"><{counter}></td>
				<td style="width: 10px;"><{$log.ms}></td>
				<td><{$log.typeName}></td>
				<td>
					<{if $log.typeName == 'DUMP'}>
						<{$log.message}>
					<{else}>
						<pre class="info <{$log.typeName}>"><{$log.message|escape}></pre>
						<{if $log.info}>
							<pre><{$log.info|escape}></pre>
						<{/if}>
					<{/if}>
				</td>
			</tr>
		<{/foreach}>
		</table>
	</div>

	<p class="h2">送信済ヘッダ</p>
	<div id="adelieDebugSentHeaders">
		<{strip}>
		<pre class="console">
			<{foreach from=$sentHeaders item="header"}>
				<{$header}><br />
			<{/foreach}>
		</pre>
		<{/strip}>
	</div>
	<p class="h2">リクエスト</p>
	<div id="adelieDebugRequest">
		<{foreach from=$requests key="name" item="request"}>
			<p class="h3"><{$name}></p>
			<{if $request}>
				<table class="data">
					<tr>
						<th>キー</th>
						<th>値</th>
					</tr>
					<{foreach from=$request key="key" item="value"}>
						<tr>
							<td><{$key}></td>
							<td><{$value|@var_dump}></td>
						</tr>
					<{/foreach}>
				</table>
			<{else}>
				<p>セットされている変数はありません。</p>
			<{/if}>
		<{/foreach}>
	</div>
</div>