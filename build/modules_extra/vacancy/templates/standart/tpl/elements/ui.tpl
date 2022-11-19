<?
$frame = trading()->get_resource_active(3, '{uid}');
?>
<div class="table-responsive" data-vacancy="{id}">
<div class="avatar"><a href="/profile?id={uid}"><img src="<?=convert_avatar('{uid}');?>" alt="{login}"></a></div>
<div class="name"><a href="/profile?id={uid}" target="_blank" title="Группа: {gp_name}" style="color: {gp_color};">{name}</a></div>
<div class="vacancy">{vacancy}</div>
<div class="status {class}">{status}</div>
</div>