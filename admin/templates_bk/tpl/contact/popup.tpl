

<table class="table-view w-full" cellspacing="0" cellpadding="0">

{if $edit.title}
<tr>
   <th class="tbl-label">Bài viết đã xem</th>
   <td>
   {$edit.title}
   </td>
</tr>
{/if}

<tr>
   <th class="tbl-label">Họ tên</th>
   <td>
   {$edit.name}
   </td>
</tr>

<tr>
   <th class="tbl-label">Điện thoại</th>
   <td>
   {$edit.phone}
   </td>
</tr>

<tr>
   <th class="tbl-label">Email</th>
   <td>
   {$edit.email}
   </td>
</tr>

{if $edit.address}
<tr>
   <th class="tbl-label">Địa chỉ</th>
   <td>
   {$edit.address}
   </td>
</tr>
{/if}

<tr>
   <th class="tbl-label">Nội dung</th>
   <td>
   {$edit.message}
   </td>
</tr>
<tr>
   <th class="tbl-label">Thời gian</th>
   <td>
   {$edit.dated|date_format:"%H:%M:%S  %d-%m-%Y"}
   </td>
</tr>

</table>
