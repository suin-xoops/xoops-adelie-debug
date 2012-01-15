<h1>Substitute User</h1>
<form action="" method="post">
	<p>選択するか uname を入力してください。</p>
	<select name="uid">
		<option value=""></option>
		<{foreach from=$users item="user"}>
			<option value="<{$user.uid}>"><{$user.uname|escape}><{if $user.name}>(<{$user.name|escape}>)<{/if}></option>
		<{/foreach}>
	</select>
	<span>uname: </span><input type="text" name="uname" size="15" /><br />
	<input type="submit" value="なりきる" />
</form>
<{if $nowUser}>
	<p>只今、<a href="<{$xoops_url}>/userinfo.php?uid=<{$nowUser.uid}>"><{$nowUser.uname}></a> としてログイン中です。 </p>
<{/if}>
