@extends('layouts.app')

@section('title','Create Group')


@section('body')

<h1>Create New Group</h1>


<form method="POST" action="/groups">

@csrf


<input 
type="text"
name="name"
placeholder="Group name"
style="padding:10px;width:300px;"
>


<br><br>


<textarea
name="description"
placeholder="Group description"
style="padding:10px;width:300px;height:100px;"
></textarea>


<br><br>


<button type="submit"
style="background:#1a3c8f;color:white;padding:10px 20px;border:none;">
Create
</button>


</form>


@endsectio