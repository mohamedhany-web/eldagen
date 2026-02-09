<form action="{{ route('courses.order', $advancedCourse) }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="orderForm">
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
        <select name="payment_method" id="payment_method" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-900">
            <option value="">اختر طريقة الدفع</option>
            <option value="code" {{ old('payment_method') == 'code' ? 'selected' : '' }}>كود التفعيل</option>
            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
            <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>أخرى</option>
        </select>
        @error('payment_method')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- حقل كود التفعيل (يظهر عند اختيار كود التفعيل) --}}
    <div id="activation_code_wrap" class="{{ old('payment_method') == 'code' ? '' : 'hidden' }}">
        <label class="block text-sm font-medium text-gray-700 mb-2">كود التفعيل</label>
        <input type="text" name="activation_code" id="activation_code" value="{{ old('activation_code') }}"
               placeholder="أدخل الكود الذي حصلت عليه"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-900 font-mono text-lg tracking-widest"
               style="text-transform: uppercase;">
        <p class="text-xs text-gray-500 mt-1">أدخل كود التفعيل لتفعيل الكورس فوراً دون انتظار المراجعة</p>
        @error('activation_code')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- صورة الإيصال (تختفي عند اختيار كود التفعيل) --}}
    <div id="payment_proof_wrap" class="{{ old('payment_method') == 'code' ? 'hidden' : '' }}">
        <label class="block text-sm font-medium text-gray-700 mb-2">صورة الإيصال</label>
        <input type="file" name="payment_proof" id="payment_proof" accept="image/*"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700">
        <p class="text-xs text-gray-500 mt-1">ارفع صورة الإيصال أو الفاتورة (jpeg, png, jpg - حد أقصى 2 ميجا)</p>
        @error('payment_proof')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات (اختياري)</label>
        <textarea name="notes" rows="3"
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-900"
                  placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
    </div>

    <button type="submit" class="w-full btn-buy-gradient justify-center py-3">
        <i class="fas fa-paper-plane"></i>
        <span id="submitBtnText">إرسال الطلب</span>
    </button>
</form>

<script>
(function() {
    var method = document.getElementById('payment_method');
    var codeWrap = document.getElementById('activation_code_wrap');
    var proofWrap = document.getElementById('payment_proof_wrap');
    var codeInput = document.getElementById('activation_code');
    var proofInput = document.getElementById('payment_proof');
    var submitText = document.getElementById('submitBtnText');

    function toggle() {
        var isCode = method.value === 'code';
        codeWrap.classList.toggle('hidden', !isCode);
        proofWrap.classList.toggle('hidden', isCode);
        if (isCode) {
            codeInput.setAttribute('required', 'required');
            proofInput.removeAttribute('required');
            proofInput.value = '';
            submitText.textContent = 'تفعيل الكورس بالكود';
        } else {
            codeInput.removeAttribute('required');
            proofInput.setAttribute('required', 'required');
            codeInput.value = '';
            submitText.textContent = 'إرسال الطلب';
        }
    }
    method.addEventListener('change', toggle);
    toggle();
    if (codeInput.value) codeInput.value = codeInput.value.toUpperCase();
    codeInput.addEventListener('input', function() { this.value = this.value.toUpperCase(); });
})();
</script>
