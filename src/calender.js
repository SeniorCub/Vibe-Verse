document.addEventListener("DOMContentLoaded", async function () {
     const API_BASE_URL = "https://api.rhinoguards.co.uk";
 
     // Function to fetch events from the API
     async function fetchEvents() {
         try {
             const response = await fetch(`${API_BASE_URL}/User/allEvents.php`, { method: "GET" });
             const data = await response.json();
 
             if (data.success) {
                 return data.data.map(event => ({
                     id: event.id,
                     title: event.eventTitle,
                     start: event.eventStart,
                     end: event.eventEnd,
                     description: event.eventDescription,
                     location: event.eventLocation
                 }));
             } else {
                 console.log("Error fetching events:", data.message);
                 return [];
             }
         } catch (error) {
             console.error("Fetch Error in fetchEvents:", error);
             return [];
         }
     }
 
     // Initialize FullCalendar
     async function initializeCalendar() {
         const events = await fetchEvents();
         const calendarEl = document.getElementById('calendar');
         if (!calendarEl) {
             console.error("Calendar element not found.");
             return;
         }
     
         const calendar = new FullCalendar.Calendar(calendarEl, {
             initialView: 'dayGridMonth',
             headerToolbar: {
                 left: 'prev,next today',
                 center: 'title',
                 right: 'dayGridMonth,dayGridWeek,timeGridDay,listMonth'
             },
             events: events,
             eventClick: function (info) {
               const eventTitleEl = document.getElementById('eventTitle');
               const eventStartEl = document.getElementById('eventStart');
               const eventEndEl = document.getElementById('eventEnd');
               const eventDescriptionEl = document.getElementById('eventDescription');
               const eventLocationEl = document.getElementById('eventLocation');
           
               if (eventTitleEl) eventTitleEl.textContent = info.event.title;
               if (eventStartEl) eventStartEl.textContent = info.event.start.toISOString().slice(0, 10);
               if (eventEndEl) eventEndEl.textContent = info.event.end ? info.event.end.toISOString().slice(0, 10) : 'N/A';
               if (eventDescriptionEl) eventDescriptionEl.textContent = info.event.extendedProps.description || 'No description available';
               if (eventLocationEl) eventLocationEl.textContent = info.event.extendedProps.location || 'No location specified';
           
               const googleCalendarButton = document.getElementById('googleCalendarButton');
               if (googleCalendarButton) {
                   googleCalendarButton.onclick = function () {
                       const googleCalendarUrl = 'https://calendar.google.com/calendar/r/eventedit?text=' +
                           encodeURIComponent(info.event.title) +
                           '&dates=' +
                           info.event.start.toISOString().replace(/-|:|\.\d\d\d/g, '') + '/' +
                           (info.event.end ? info.event.end.toISOString().replace(/-|:|\.\d\d\d/g, '') : info.event.start.toISOString().replace(/-|:|\.\d\d\d/g, '')) +
                           '&details=' + encodeURIComponent('Event details here...');
                       window.open(googleCalendarUrl, '_blank');
                   };
               }
           
               const icalButton = document.getElementById('icalButton');
               if (icalButton) {
                   icalButton.onclick = function () {
                       const icalData = 'BEGIN:VCALENDAR\nVERSION:2.0\nBEGIN:VEVENT\n' +
                           'SUMMARY:' + info.event.title + '\n' +
                           'DTSTART:' + info.event.start.toISOString().replace(/-|:|\.\d\d\d/g, '') + '\n' +
                           'DTEND:' + (info.event.end ? info.event.end.toISOString().replace(/-|:|\.\d\d\d/g, '') : info.event.start.toISOString().replace(/-|:|\.\d\d\d/g, '')) + '\n' +
                           'END:VEVENT\nEND:VCALENDAR';
           
                       const blob = new Blob([icalData], { type: 'text/calendar;charset=utf-8' });
                       const url = URL.createObjectURL(blob);
                       const a = document.createElement('a');
                       a.href = url;
                       a.download = info.event.title + '.ics';
                       document.body.appendChild(a);
                       a.click();
                       document.body.removeChild(a);
                   };
               }
           
               // Show the modal
               const eventDetailsModal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
               eventDetailsModal.show();
           }           
         });
 
         calendar.render();
     }
 
     // Initialize the calendar with events from the API
     initializeCalendar();
 }); 