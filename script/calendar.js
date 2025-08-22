document.addEventListener('DOMContentLoaded', () => {
    const monthYearEl = document.getElementById('monthYear');
    const calendarGrid = document.getElementById('calendarGrid');
    const prevMonthBtn = document.getElementById('prevMonthBtn');
    const nextMonthBtn = document.getElementById('nextMonthBtn');

    let currentDate = new Date();

    // Debug: Log the feedback data to console
    console.log('Feedback Data:', feedbackData);
    console.log('Is Admin:', isAdmin);

    const renderCalendar = () => {
        const month = currentDate.getMonth();
        const year = currentDate.getFullYear();

        monthYearEl.textContent = `${currentDate.toLocaleString('default', { month: 'long' })} ${year}`;

        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const lastDateOfMonth = new Date(year, month + 1, 0).getDate();
        const lastDateOfPrevMonth = new Date(year, month, 0).getDate();

        calendarGrid.innerHTML = '';

        // Add days from the previous month
        for (let i = firstDayOfMonth; i > 0; i--) {
            const dayEl = document.createElement('div');
            dayEl.classList.add('day', 'prev-month');
            const prevMonthDay = lastDateOfPrevMonth - i + 1;
            dayEl.textContent = prevMonthDay;
            
            // Add feedback indicators for previous month days
            const prevMonth = month === 0 ? 11 : month - 1;
            const prevYear = month === 0 ? year - 1 : year;
            addFeedbackIndicators(dayEl, prevMonthDay, prevMonth, prevYear);
            
            calendarGrid.appendChild(dayEl);
        }

        // Add days of the current month
        for (let i = 1; i <= lastDateOfMonth; i++) {
            const dayEl = document.createElement('div');
            dayEl.classList.add('day');
            dayEl.textContent = i;

            // Highlight today's date
            const today = new Date();
            if (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                dayEl.classList.add('today');
                
                // Test: Add a test feedback indicator for admin users
                if (isAdmin) {
                    const testIndicator = document.createElement('div');
                    testIndicator.className = 'feedback-indicator admin-feedback';
                    testIndicator.textContent = 'TEST';
                    testIndicator.title = 'Test admin indicator';
                    testIndicator.style.zIndex = '1000';
                    testIndicator.style.border = '2px solid yellow';
                    dayEl.appendChild(testIndicator);
                    console.log('Added test admin indicator to today');
                }
            }

            // Add feedback indicators
            addFeedbackIndicators(dayEl, i, month, year);

            calendarGrid.appendChild(dayEl);
        }

        // Add days from the next month to fill the grid
        const totalDaysDisplayed = firstDayOfMonth + lastDateOfMonth;
        const remainingDays = 42 - totalDaysDisplayed; // 6 rows * 7 days = 42
        
        for (let i = 1; i <= remainingDays; i++) {
            const dayEl = document.createElement('div');
            dayEl.classList.add('day', 'next-month');
            dayEl.textContent = i;
            
            // Add feedback indicators for next month days
            const nextMonth = month === 11 ? 0 : month + 1;
            const nextYear = month === 11 ? year + 1 : year;
            addFeedbackIndicators(dayEl, i, nextMonth, nextYear);
            
            calendarGrid.appendChild(dayEl);
        }
    };

    const addFeedbackIndicators = (dayEl, day, month, year) => {
        const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        
        // Debug: Log the date being checked
        console.log('Checking date:', dateString, 'Data:', feedbackData[dateString]);
        
        if (feedbackData && feedbackData[dateString]) {
            console.log('Found feedback data for date:', dateString, 'Value:', feedbackData[dateString]);
            
            if (isAdmin) {
                // Admin view: show count of feedback submissions
                const count = feedbackData[dateString];
                console.log('Creating admin indicator with count:', count);
                
                const indicator = document.createElement('div');
                indicator.className = 'feedback-indicator admin-feedback';
                indicator.textContent = count;
                indicator.title = `${count} feedback submission${count > 1 ? 's' : ''} on ${dateString}`;
                indicator.style.zIndex = '1000'; // Ensure it's visible
                dayEl.appendChild(indicator);
                
                console.log('Admin indicator created and added:', indicator);
            } else {
                // Student view: show feedback details
                const feedbacks = feedbackData[dateString];
                feedbacks.forEach(feedback => {
                    const indicator = document.createElement('div');
                    indicator.className = `feedback-indicator ${feedback.rating >= 4 ? 'high-rating' : 'low-rating'}`;
                    indicator.innerHTML = `<span class="rating">${feedback.rating}★</span><span class="category">${feedback.category}</span>`;
                    indicator.title = `${feedback.category} - ${feedback.rating} stars on ${dateString}`;
                    dayEl.appendChild(indicator);
                });
            }
        }
    };

    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    renderCalendar();
});
