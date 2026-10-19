<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan Details - {{ $plan->name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass { backdrop-filter: saturate(180%) blur(10px); background: rgba(255,255,255,0.75); }
    </style>
    <script>
        function copyToClipboard(text, noteId){
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function(){
                    var n = document.getElementById(noteId); n.classList.remove('hidden'); setTimeout(function(){ n.classList.add('hidden'); }, 1500);
                });
            } else {
                const el = document.createElement('textarea'); el.value = text; document.body.appendChild(el); el.select(); document.execCommand('copy'); document.body.removeChild(el);
                var n = document.getElementById(noteId); n.classList.remove('hidden'); setTimeout(function(){ n.classList.add('hidden'); }, 1500);
            }
        }
    </script>
    </head>
<body class="bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="rounded-2xl p-6 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 text-white shadow-lg">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <a href="{{ route('knowledge.index') }}" class="inline-flex items-center text-sm text-white/90 hover:text-white">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Back to Plans
                    </a>
                    <h1 class="text-3xl font-extrabold mt-2">{{ $plan->name }}</h1>
                    <p class="text-white/90">Choose the best option for your growth</p>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-extrabold">₹{{ number_format($plan->price) }}<span class="text-xl align-top">/-</span></div>
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full bg-white/20 text-white text-sm">
                        <i class="fa-regular fa-clock mr-2"></i> Validity: {{ $plan->validity }}
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white shadow rounded-2xl p-6">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <div class="text-sm text-slate-500">Plan</div>
                                <div class="text-xl font-semibold">{{ $plan->name }}</div>
                            </div>
                        </div>
                        <div class="rounded-xl bg-amber-50 text-amber-700 px-3 py-2 text-sm">
                            <i class="fa-solid fa-gift mr-2"></i> {{ $plan->cashback_description }}
                        </div>
                    </div>

                    <h2 class="text-lg font-semibold mt-6 mb-3">What's included</h2>
                    <ul class="space-y-3">
                        @foreach($plan->features as $feature)
                            <li class="flex items-start gap-3">
                                @php
                                    $status = $feature->feature_status;
                                    $icon = 'fa-circle-info'; $color = 'text-slate-500 bg-slate-100';
                                    if ($status === 'yes') { $icon = 'fa-circle-check'; $color = 'text-emerald-600 bg-emerald-50'; }
                                    elseif ($status === 'no') { $icon = 'fa-circle-xmark'; $color = 'text-rose-600 bg-rose-50'; }
                                    elseif ($status === 'paid') { $icon = 'fa-indian-rupee-sign'; $color = 'text-amber-600 bg-amber-50'; }
                                    elseif ($status === 'email') { $icon = 'fa-envelope'; $color = 'text-sky-600 bg-sky-50'; }
                                @endphp
                                <span class="mt-0.5 inline-flex items-center justify-center w-7 h-7 rounded-full {{ $color }}">
                                    <i class="fa-solid {{ $icon }} text-sm"></i>
                                </span>
                                <div class="flex-1">
                                    <div class="font-medium text-slate-800">{{ $feature->feature_name }}</div>
                                    <div class="text-xs text-slate-500 capitalize">Status: {{ $feature->feature_status }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-8">
                        <!-- <a href="#" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 shadow">
                            <i class="fa-solid fa-bolt"></i> Proceed to Subscribe
                        </a> -->
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white shadow rounded-2xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <h3 class="text-lg font-semibold">Bank Details</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Bank Name</span>
                            <span class="font-medium">Punjab National Bank</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Account Number</span>
                            <span class="font-medium">6006002100001329</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">IFSC</span>
                            <span class="font-medium">PUNB0600600</span>
                        </div>
                        <!-- <div class="flex items-center justify-between">
                            <span class="text-slate-500">Bank</span>
                            <span class="font-medium">Demo Bank</span>
                        </div> -->
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Branch</span>
                            <span class="font-medium">Chaugain</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow rounded-2xl p-6 text-center">
                    <div class="flex items-center gap-3 mb-4 justify-center">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <h3 class="text-lg font-semibold">Scan & Pay</h3>
                    </div>
                    <div class="p-3 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 inline-block border">
                        <img src="{{ asset('public/backend/images/qrnew.png') }}" alt="Scan to Pay QR" class="w-52 h-52 object-contain" />
                    </div>
                    <p class="text-xs text-slate-500 mt-3">Use any UPI app to scan and pay</p>
                </div>

                <div class="bg-white shadow rounded-2xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                            <i class="fa-solid fa-indian-rupee-sign"></i>
                        </div>
                        <h3 class="text-lg font-semibold">UPI ID</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="upiId" type="text" readonly value="9431058741m@pnb" class="flex-1 bg-slate-50 border rounded-xl px-3 py-2 text-sm">
                        <button type="button" class="px-3 py-2 text-sm rounded-xl bg-emerald-600 text-white hover:bg-emerald-700"
                                onclick="copyToClipboard(document.getElementById('upiId').value, 'copyNote')">
                            <i class="fa-regular fa-copy mr-1"></i> Copy
                        </button>
                    </div>
                    <p id="copyNote" class="text-xs text-emerald-600 mt-2 hidden">Copied to clipboard</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

