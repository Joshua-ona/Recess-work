 @extends('layouts.app')
@section('title', 'Discussions')

@section('body')

<div>
    
<div style="display:flex;height:100vh;font-family:sans-serif;">
    
    {{-- Main Content --}}
    <div style="flex:1;padding:30px;overflow-y:auto;background:#f5f6fa;">

        {{-- Header --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h1>Discussion Forum</h1>
            <button onclick="document.getElementById('new-post-form').style.display='block'" 
                style="background:#1a3c8f;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;">
                + New Discussion
            </button>
        </div>

        {{-- Filter by Topic --}}
        <div style="margin-bottom:20px;">
           <select onchange="if(this.value=='other'){document.getElementById('custom-topic').style.display='block'}else{document.getElementById('custom-topic').style.display='none'}" 
    style="width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:5px;">
    <option value="">Select Topic e.g. Mathematics</option>
    <option value="programming">Programming</option>
    <option value="mathematics">Mathematics</option>
    <option value="science">Science</option>
    <option value="general">General</option>
    <option value="other">Other (type your own)</option>
</select>

<input type="text" id="custom-topic" placeholder="Type your topic here..." 
    style="display:none;width:100%;padding:10px;margin:5px 0;border:1px solid #ccc;border-radius:5px;box-sizing:border-box;">
        </div>

        {{-- New Post Form --}}
        <div id="new-post-form" style="display:none;background:white;padding:20px;border-radius:10px;margin-bottom:20px;box-shadow:0 2px 5px rgba(0,0,0,0.1);">
            <h3>Start a New Discussion</h3>
            <input type="text" placeholder="Title" style="width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:5px;box-sizing:border-box;">
            <textarea placeholder="Write your message..." style="width:100%;padding:10px;height:100px;border:1px solid #ccc;border-radius:5px;box-sizing:border-box;"></textarea>
            <select style="width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:5px;">
                <option>Select Topic</option>
                <option>Programming</option>
                <option>Mathematics</option>
                <option>Science</option>
                <option>General</option>
            </select>
            <div style="display:flex;gap:10px;">
                <button style="background:#1a3c8f;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;">Post</button>
                <button onclick="document.getElementById('new-post-form').style.display='none'" 
                    style="background:#ccc;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;">Cancel</button>
            </div>
        </div>

        {{-- Discussion Posts --}}
        <div style="background:white;border-radius:10px;padding:20px;box-shadow:0 2px 5px rgba(0,0,0,0.1);margin-bottom:15px;">
            <div style="display:flex;justify-content:space-between;">
                <h3>How do I connect Java to MySQL?</h3>
                <span style="background:#e8f0fe;color:#1a3c8f;padding:5px 10px;border-radius:20px;font-size:12px;">Programming</span>
            </div>
            <p style="color:#666;margin:10px 0;">I keep getting connection refused error when trying to connect...</p>
            <div style="display:flex;gap:10px;margin-top:10px;">
                <button onclick="document.getElementById('reply-1').style.display='block'" style="background:none;border:1px solid #ccc;padding:5px 10px;border-radius:5px;cursor:pointer;">💬 Reply</button>
<button style="background:none;border:1px solid #ccc;padding:5px 10px;border-radius:5px;cursor:pointer;">📄 Export PDF</button>
<button style="background:none;border:1px solid #ccc;padding:5px 10px;border-radius:5px;cursor:pointer;">📤 Share</button>
                <div id="reply-1" style="display:none;margin-top:10px;">
    <textarea placeholder="Write your reply..." 
        style="width:100%;padding:10px;height:80px;border:1px solid #ccc;border-radius:5px;box-sizing:border-box;"></textarea>
    <button style="background:#1a3c8f;color:white;padding:8px 15px;border:none;border-radius:5px;cursor:pointer;margin-top:5px;">
        Submit Reply
    </button>
</div>
            </div>
        </div>

        <div style="background:white;border-radius:10px;padding:20px;box-shadow:0 2px 5px rgba(0,0,0,0.1);margin-bottom:15px;">
            <div style="display:flex;justify-content:space-between;">
                <h3>Tips for effective study and time management</h3>
                <span style="background:#e8f0fe;color:#1a3c8f;padding:5px 10px;border-radius:20px;font-size:12px;">General</span>
            </div>
            <p style="color:#666;margin:10px 0;">Let's share some proven strategies that really help...</p>
            <div style="display:flex;gap:10px;margin-top:10px;">
                <button onclick="document.getElementById('reply-2').style.display='block'" style="background:none;border:1px solid #ccc;padding:5px 10px;border-radius:5px;cursor:pointer;">💬 Reply</button>
<button style="background:none;border:1px solid #ccc;padding:5px 10px;border-radius:5px;cursor:pointer;">📄 Export PDF</button>
<button style="background:none;border:1px solid #ccc;padding:5px 10px;border-radius:5px;cursor:pointer;">📤 Share</button>
                <div id="reply-2" style="display:none;margin-top:10px;">
    <textarea placeholder="Write your reply..." 
        style="width:100%;padding:10px;height:80px;border:1px solid #ccc;border-radius:5px;box-sizing:border-box;"></textarea>
    <button style="background:#1a3c8f;color:white;padding:8px 15px;border:none;border-radius:5px;cursor:pointer;margin-top:5px;">
        Submit Reply
    </button>
</div>
            </div>
        </div>

    </div>
</div>
@endsection
</div>
