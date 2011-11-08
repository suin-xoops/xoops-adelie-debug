<style><{$css}></style>
<div class="adelieDebug">
	<p class="h1">
		<span>Adelie Debug</span>
		<{if $isBuild}>
			<span style="font-size: 12px;">(Build <{'YmdHis'|date:$buildTime}>)</sapn>
		<{else}>
			<span style="font-size: 12px;">(Source)</sapn>
		<{/if}>
	</p>
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