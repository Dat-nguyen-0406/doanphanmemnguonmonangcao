@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-red-600 mb-6">DEBUG: VNPay Payment</h1>

            <div class="bg-gray-900 text-green-400 p-4 rounded font-mono text-sm overflow-x-auto mb-6">
                <div><strong>Full Payment URL:</strong></div>
                <div class="break-all">{{ $payment_url }}</div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 p-4 rounded">
                    <h3 class="font-bold text-lg mb-2">Input Data (sorted)</h3>
                    <pre class="text-xs overflow-x-auto">{{ json_encode($input_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>

                <div class="bg-gray-50 p-4 rounded">
                    <h3 class="font-bold text-lg mb-2">Query String</h3>
                    <pre class="text-xs overflow-x-auto">{{ $query_string }}</pre>
                </div>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <p><strong>Hash Secret:</strong> {{ env('VNP_HASHSECRET') }}</p>
                <p><strong>TmnCode:</strong> {{ env('VNP_TMNCODE') }}</p>
                <p><strong>Secure Hash:</strong> {{ $secure_hash }}</p>
            </div>

            <div class="text-center">
                <form action="{{ $payment_url }}" method="GET" id="debugForm">
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700">
                        Test redirect to VNPay
                    </button>
                </form>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded">
                <p class="text-sm"><strong>Hướng dẫn:</strong> Nếu lỗi chữ kí vẫn xuất hiện, hãy copy toàn bộ thông tin trên gửi cho support VNPay để kiểm tra.</p>
            </div>
        </div>
    </div>
</div>
@endsection
