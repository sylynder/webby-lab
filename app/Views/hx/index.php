<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="{{asset('js/htmx.min2.0.3.js')}}"></script>
    <script src="{{asset('tailwind.css')}}"></script>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
</head>
<body>
    <div class="text-center">
        <h1 class="text-2xl font-bold my-5">
            Simple request example
        </h1>
        <!-- <button hx-get="https://jsonplaceholder.typicode.com/users" hx-swap="outerHTML" class="bg-blue-500 text-white py-2 px-3 my-5 rounded-lg">
            Fetch
        </button> -->
        <!-- <button 
            hx-get="/hx/users" 
            hx-swap="outerHTML" 
            class="bg-blue-500 text-white py-2 px-3 my-5 rounded-lg">
            Fetch
        </button> -->

        <button 
            hx-get="/hx/users" 
            hx-target="#users"
            hx-indicator="#loading"
            hx-vals='{"limit": 5}'
            class="bg-blue-500 text-white py-2 px-3 my-5 rounded-lg">
            Fetch
        </button>

        <span class="htmx-indicator text-center " id="loading">
            <img src="{{asset('images/sample.gif')}}" class="mx-auto h-10">
        </span>

        <div id="users"></div>
    </div>
   
</body>
</html>