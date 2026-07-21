<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a meeting with <?= htmlspecialchars($hostUser['name']) ?> - MiniCal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#fafafa] text-slate-900 min-h-screen flex items-center justify-center p-4 lg:p-10 selection:bg-black selection:text-white">
    <div class="w-full max-w-5xl bg-white border border-slate-200/90 rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden grid grid-cols-1 lg:grid-cols-12">
        
        <!-- Host & Event Sidebar Info -->
        <div class="lg:col-span-5 bg-slate-50/60 border-b lg:border-b-0 lg:border-r border-slate-200 p-8 space-y-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-black text-white rounded-2xl flex items-center justify-center text-xl font-bold shadow-sm">
                    <?= strtoupper(substr($hostUser['name'], 0, 2)) ?>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-950 tracking-tight"><?= htmlspecialchars($hostUser['name']) ?></h2>
                    <p class="text-xs text-slate-500 font-mono">/u/<?= htmlspecialchars($hostUser['username']) ?></p>
                </div>
            </div>

            <?php if (!empty($profile['bio'])): ?>
                <p class="text-slate-600 text-sm leading-relaxed"><?= htmlspecialchars($profile['bio']) ?></p>
            <?php endif; ?>

            <!-- Multi-Event Selector Tabs if host has >1 event -->
            <?php if (count($allEvents) > 1): ?>
                <div class="space-y-2 pt-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Select Consultation Service:</span>
                    <div class="space-y-1.5">
                        <?php foreach ($allEvents as $evOption): ?>
                            <a href="<?= APP_URL ?>/u/<?= htmlspecialchars($hostUser['username']) ?>?event=<?= htmlspecialchars($evOption['slug']) ?>"
                               class="flex items-center justify-between p-3 rounded-xl border text-xs font-bold transition-all <?= ($event['id'] === $evOption['id']) ? 'bg-black text-white border-black shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100' ?>">
                                <span><?= htmlspecialchars($evOption['name']) ?></span>
                                <span class="<?= ($event['id'] === $evOption['id']) ? 'text-slate-300' : 'text-slate-400' ?>"><?= $evOption['duration_minutes'] ?>m</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="border-t border-slate-200/80 pt-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-950"><?= htmlspecialchars($event['name'] ?? '30 Minute Meeting') ?></h3>
                
                <div class="flex items-center gap-3 text-slate-700 text-xs font-semibold">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span><?= htmlspecialchars($event['duration_minutes'] ?? 30) ?> Minutes</span>
                </div>

                <div class="flex items-center gap-3 text-slate-700 text-xs font-semibold">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Book up to <?= htmlspecialchars($event['booking_window_days'] ?? 30) ?> days in advance</span>
                </div>

                <div class="flex items-center gap-3 text-slate-700 text-xs font-semibold">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span class="capitalize"><?= htmlspecialchars($event['location_type'] ?? 'online') ?></span>
                </div>

                <div class="flex items-center gap-3 text-slate-700 text-xs font-semibold">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V11a2 2 0 012-2h1.065"></path></svg>
                    <span>Timezone: <?= htmlspecialchars($profile['timezone'] ?? 'UTC') ?></span>
                </div>

                <?php if (!empty($event['is_paid'])): ?>
                    <div class="p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-800 uppercase">Paid Appointment</span>
                        <span class="text-lg font-extrabold text-slate-950">$<?= number_format($event['price'], 2) ?> <?= htmlspecialchars($event['currency']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Date & Slot Picker / Booking Form -->
        <div class="lg:col-span-7 p-8 flex flex-col justify-between">
            <div id="step-schedule">
                <h3 class="text-lg font-bold text-slate-950 mb-4">Select Date & Time</h3>
                
                <?php
                $minDate = date('Y-m-d');
                $maxWindowDays = (int)($event['booking_window_days'] ?? 30);
                $maxDate = date('Y-m-d', strtotime("+{$maxWindowDays} days"));
                ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date Input restricted to advance booking window -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Select Date</label>
                        <input type="date" id="booking-date" min="<?= $minDate ?>" max="<?= $maxDate ?>" value="<?= $minDate ?>"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <p class="text-[11px] text-slate-400 mt-1">Bookings allowed until <?= date('M j, Y', strtotime($maxDate)) ?></p>
                    </div>

                    <!-- Available Time Slots -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Available Slots</label>
                        <div id="slots-container" class="max-h-60 overflow-y-auto space-y-2 pr-1">
                            <p class="text-xs text-slate-400">Loading available slots...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Form Step -->
            <form id="booking-form" class="mt-8 border-t border-slate-100 pt-6 space-y-4">
                <input type="hidden" id="selected-start-time" name="start_time" value="">
                <input type="hidden" id="selected-end-time" name="end_time" value="">
                <input type="hidden" id="selected-booking-date" name="booking_date" value="">
                <input type="hidden" name="event_slug" value="<?= htmlspecialchars($event['slug'] ?? '') ?>">

                <div id="form-error" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-xs font-semibold"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Your Full Name *</label>
                        <input type="text" name="customer_name" required placeholder="Jane Smith"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Your Email Address *</label>
                        <input type="email" name="customer_email" required placeholder="jane@example.com"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                </div>

                <!-- Custom Fields Assigned to This Specific Event -->
                <?php foreach ($customFields as $field): ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            <?= htmlspecialchars($field['label']) ?> <?= $field['is_required'] ? '*' : '' ?>
                        </label>
                        
                        <?php
                        $fieldKey = 'field_' . $field['id'];
                        $reqAttr = $field['is_required'] ? 'required' : '';
                        $placeholder = htmlspecialchars($field['placeholder'] ?? '');
                        $options = !empty($field['options']) ? json_decode($field['options'], true) : [];
                        ?>

                        <?php if ($field['field_type'] === 'textarea'): ?>
                            <textarea name="<?= $fieldKey ?>" <?= $reqAttr ?> placeholder="<?= $placeholder ?>" rows="2"
                                      class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black"></textarea>
                        
                        <?php elseif ($field['field_type'] === 'select'): ?>
                            <select name="<?= $fieldKey ?>" <?= $reqAttr ?> class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                                <option value="">Select an option</option>
                                <?php foreach ($options as $opt): ?>
                                    <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php else: ?>
                            <input type="<?= $field['field_type'] === 'number' ? 'number' : ($field['field_type'] === 'date' ? 'date' : 'text') ?>"
                                   name="<?= $fieldKey ?>" <?= $reqAttr ?> placeholder="<?= $placeholder ?>"
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <button type="submit" id="submit-btn" disabled
                        class="w-full py-3.5 px-4 bg-black hover:bg-slate-800 disabled:bg-slate-200 disabled:text-slate-400 text-white font-semibold text-sm rounded-xl shadow-md transition-all transform active:scale-[0.98]">
                    Confirm Booking
                </button>
            </form>
        </div>
    </div>

    <script>
        const username = "<?= htmlspecialchars($hostUser['username']) ?>";
        const eventSlug = "<?= htmlspecialchars($event['slug'] ?? '') ?>";
        const dateInput = document.getElementById('booking-date');
        const slotsContainer = document.getElementById('slots-container');
        const bookingForm = document.getElementById('booking-form');
        const submitBtn = document.getElementById('submit-btn');
        const formError = document.getElementById('form-error');

        function fetchSlots(date) {
            slotsContainer.innerHTML = '<p class="text-xs text-slate-400">Loading available slots...</p>';
            submitBtn.disabled = true;

            fetch(`<?= APP_URL ?>/u/${username}/slots?date=${date}&event=${eventSlug}`)
                .then(res => res.json())
                .then(data => {
                    slotsContainer.innerHTML = '';
                    if (!data.slots || data.slots.length === 0) {
                        slotsContainer.innerHTML = '<p class="text-xs text-amber-800 bg-amber-50 p-3 rounded-xl border border-amber-200">No time slots available for this date.</p>';
                        return;
                    }

                    data.slots.forEach(slot => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'w-full py-2.5 px-4 bg-slate-50 hover:bg-black hover:text-white border border-slate-200 text-slate-900 text-xs font-bold rounded-xl text-left transition-all flex items-center justify-between slot-btn';
                        btn.innerHTML = `<span>${slot.display}</span><svg class="w-4 h-4 opacity-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
                        
                        btn.onclick = () => {
                            document.querySelectorAll('.slot-btn').forEach(b => {
                                b.classList.remove('bg-black', 'text-white');
                                b.classList.add('bg-slate-50', 'text-slate-900');
                                b.querySelector('svg').classList.add('opacity-0');
                            });
                            btn.classList.remove('bg-slate-50', 'text-slate-900');
                            btn.classList.add('bg-black', 'text-white');
                            btn.querySelector('svg').classList.remove('opacity-0');

                            document.getElementById('selected-booking-date').value = date;
                            document.getElementById('selected-start-time').value = slot.start_time;
                            document.getElementById('selected-end-time').value = slot.end_time;
                            submitBtn.disabled = false;
                        };
                        slotsContainer.appendChild(btn);
                    });
                })
                .catch(() => {
                    slotsContainer.innerHTML = '<p class="text-xs text-red-600">Failed to load time slots.</p>';
                });
        }

        dateInput.addEventListener('change', (e) => fetchSlots(e.target.value));
        fetchSlots(dateInput.value);

        bookingForm.addEventListener('submit', (e) => {
            e.preventDefault();
            formError.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Processing...';

            const formData = new FormData(bookingForm);
            fetch(`<?= APP_URL ?>/u/${username}/book`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    formError.innerText = data.message || 'Booking failed.';
                    formError.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Confirm Booking';
                }
            })
            .catch(() => {
                formError.innerText = 'Network error. Please try again.';
                formError.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.innerText = 'Confirm Booking';
            });
        });
    </script>
</body>
</html>
