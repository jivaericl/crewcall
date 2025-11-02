# Show Calling Interface Documentation

## Overview

The Show Calling Interface is the production control center of the PLANNR application. It provides real-time cue execution capabilities for live event production, allowing show callers and technical directors to manage and execute cues during live performances. The interface integrates segments, cues, content files, and user assignments into a unified control system.

## System Purpose

The show calling interface serves as the command center during live events. Production teams use this interface to execute lighting cues, trigger audio playback, advance video content, manage presentations, and coordinate all technical elements of a live show. The system provides multiple viewing modes to accommodate different workflow preferences and ensures smooth execution through features like standby mode, auto-advance, and real-time status tracking.

## Key Features

### Dual View Modes

The interface offers two distinct viewing modes to accommodate different production workflows. **Timeline View** presents cues organized by segments in a chronological flow, making it easy to see the overall structure of the show. Each segment appears as a distinct section with its own header showing timing and personnel information. Cues within each segment are displayed as large, easy-to-read cards with all relevant information visible at a glance. This view is ideal for understanding the show structure and seeing context around each cue.

**Table View** provides a compact, tabular display of all cues sorted by time. This view shows more cues on screen simultaneously, making it easier to scan ahead and see what's coming. The table includes columns for time, segment, cue number, type, description, operator, status, and actions. This view is preferred by experienced show callers who want maximum information density.

### Real-Time Clock

A prominent clock display shows the current time in large, easy-to-read numbers at the top of the interface. The clock updates every second, providing a constant time reference for the production team. This helps show callers stay on schedule and anticipate upcoming cues based on their scheduled times.

### Cue Execution Workflow

The interface implements a professional three-stage execution workflow. Cues begin in **Pending** status, indicating they haven't been called yet. The show caller selects a cue and places it in **Standby** mode, alerting the operator to prepare for execution. When ready, the show caller triggers the **GO** command, marking the cue as complete and automatically advancing to the next pending cue. This workflow prevents accidental execution while ensuring smooth transitions between cues.

### Session Management

Production teams can work with multiple sessions within an event. The session selector at the top of the interface allows quick switching between different sessions, such as morning keynote, afternoon breakout, or evening reception. When a session is selected, the interface displays only the segments and cues for that session, keeping the view focused and relevant.

### Filtering and Search

Multiple filtering options help show callers focus on specific aspects of the production. The **Segment Filter** shows only cues from a selected segment, useful when working on a specific section of the show. The **Cue Type Filter** displays only cues of a particular type, such as showing only lighting cues or only audio cues. The **Show Completed** toggle controls whether finished cues remain visible or are hidden to reduce clutter.

### Visual Status Indicators

The interface uses color coding and visual cues to communicate status at a glance. Cues in standby mode are highlighted with a yellow background and ring, making them impossible to miss. Completed cues show a green background, while skipped cues appear grayed out. Priority indicators use red for high priority and yellow for medium priority, ensuring critical cues receive appropriate attention.

### Auto-Advance

After executing or skipping a cue, the system automatically identifies the next pending cue and places it in standby mode. This feature streamlines the show calling process, reducing the number of manual actions required and helping maintain show momentum. Show callers can focus on timing and execution rather than hunting for the next cue.

### Content Integration

Cues that reference content files display the filename directly in the interface. This integration ensures operators know exactly which media file to play. The filename appears in blue text with a file icon, making it easy to spot. Future enhancements will provide direct links to preview or download the referenced content.

## User Interface Components

### Header Section

The header provides essential context and navigation. On the left side, the event name and selected session information are displayed prominently. On the right side, the real-time clock shows the current time in large, bold numbers with a label indicating it's the current time. This layout ensures show callers always know what event they're working on and what time it is.

### Control Bar

The control bar sits below the header and contains all primary controls. The session selector dropdown allows switching between sessions. View mode buttons toggle between Table and Timeline views. The Show Completed checkbox controls visibility of finished cues. The Reset Filters button clears all active filters and returns the view to its default state.

### Filter Section

When a session is selected, the filter section appears with two dropdown menus. The Segment Filter dropdown lists all segments in the current session, allowing selection of a specific segment to view. The Cue Type Filter dropdown lists all available cue types, enabling filtering by type such as Lighting, Audio, or Video.

### Timeline View Layout

In timeline view, segments appear as distinct cards with gradient blue headers. Each segment header shows the segment name, time range, code (if present), client name, and producer name. Below the header, cues are displayed as individual cards with comprehensive information including time, cue number, cue type badge, status badge, priority indicator, name, description, filename, operator, and notes.

Action buttons appear on the right side of each cue card. Pending cues show a Standby button. Cues in standby show a large GO button and a Skip button. Completed or skipped cues show a Reset button to return them to pending status.

### Table View Layout

The table view presents cues in a compact, sortable table with eight columns. The Time column shows the scheduled execution time in 12-hour format. The Segment column displays which segment contains the cue. The Cue Number column shows the cue identifier. The Type column displays a color-coded badge for the cue type. The Description column shows the cue name and truncated description with filename if present. The Operator column lists the assigned operator. The Status column shows a color-coded status badge. The Actions column contains execution buttons appropriate to the current status.

## Cue Execution Process

### Standard Execution Flow

The typical workflow for executing a cue follows a clear sequence. First, the show caller identifies the next cue to execute based on timing and show flow. They click the Standby button, which highlights the cue in yellow and updates its status to standby. This alerts the assigned operator to prepare for execution. When the moment arrives, the show caller clicks the large GO button. The system marks the cue as complete, changes the background to green, and automatically advances to the next pending cue by placing it in standby mode.

### Skipping Cues

Sometimes cues need to be skipped due to time constraints or show changes. When a cue is in standby mode, the show caller can click the Skip button instead of GO. The system marks the cue as skipped, grays it out, and auto-advances to the next pending cue. Skipped cues can be reset if needed.

### Resetting Cues

If a cue was executed or skipped by mistake, or if the show needs to be rehearsed again, cues can be reset to pending status. Clicking the Reset button on a completed or skipped cue returns it to pending status and removes any standby designation. This allows the cue to be executed again.

### Manual Standby Selection

While auto-advance typically handles standby selection, show callers can manually select any pending cue for standby. This is useful when jumping ahead in the show, preparing for a specific segment, or handling non-linear show flow. Simply clicking Standby on any pending cue places it in standby mode.

## Integration with Other Modules

### Segment Integration

The show calling interface displays all segments from the selected session in chronological order. Segment information including name, code, time range, client, and producer provides context for the cues within. Segments serve as organizational containers, grouping related cues together and making the show structure clear.

### Cue Integration

All cues from the selected session are displayed with complete information. The interface shows cue number, name, description, time, type, priority, status, operator, filename, and notes. Cues are sorted by time within their segments, creating a natural flow through the show. The cue type system provides color-coded badges for quick visual identification.

### Content Integration

Cues that reference content files display the filename in the interface. This connection ensures operators know which media file to play for each cue. The filename appears in blue text with a file icon, making it easy to spot. Future enhancements will provide direct access to content files from the show calling interface.

### User Integration

Operator assignments are displayed for each cue, showing who is responsible for execution. Client and producer information appears in segment headers, providing context about personnel involved in each section of the show. This integration ensures everyone knows their responsibilities.

### Audit Integration

All cue status changes are automatically logged through the audit system. When a cue is placed in standby, executed, skipped, or reset, the system records who made the change, when it occurred, and what changed. This creates a complete record of show execution for post-event analysis.

## Access and Navigation

### Accessing Show Calling

The show calling interface is accessed from the Events list. Each event has a prominent "Show Calling" button with a play icon, styled in primary color to stand out. Clicking this button opens the show calling interface for that event. If the event has sessions, the first session is automatically selected.

### Route Structure

The show calling interface uses two routes. The primary route `/events/{eventId}/show-call` opens the interface with automatic session selection. The session-specific route `/events/{eventId}/show-call/{sessionId}` opens the interface with a particular session pre-selected. This allows direct linking to specific sessions.

### Navigation Within Interface

Once in the show calling interface, users can switch between sessions using the dropdown selector. The view mode buttons toggle between Table and Timeline views. Filters can be applied and cleared using the filter dropdowns and reset button. All navigation is instant without page reloads thanks to Livewire.

## Best Practices

### Pre-Show Preparation

Before the show begins, review all cues in the session to ensure they're properly configured. Verify that operator assignments are correct and that all content files are uploaded and referenced correctly. Check that cue times are accurate and in the correct sequence. Use the Timeline view to understand the overall show structure and identify any potential issues.

### During Show Execution

During the live event, use the view mode that best suits your workflow. Many show callers prefer Timeline view for its clear visual organization, while others prefer Table view for its information density. Keep the Show Completed checkbox unchecked to hide finished cues and reduce clutter. Watch the real-time clock to stay on schedule. Use the auto-advance feature to maintain momentum, but don't hesitate to manually select standby cues when needed.

### Post-Show Review

After the show, enable Show Completed to see all cues including those that were executed or skipped. Review the audit logs to see exactly when each cue was executed and by whom. This information is valuable for post-event analysis and improving future shows. Reset all cues if you need to rehearse or repeat the show.

### Multi-Session Events

For events with multiple sessions, use the session selector to switch between different parts of the event. Each session maintains its own cue states, so executing cues in the morning session doesn't affect the afternoon session. This allows different show callers to work on different sessions independently.

## Technical Details

### Component Architecture

The show calling interface is built as a Livewire component located at `App\Livewire\ShowCall\Index`. The component handles session selection, view mode switching, filtering, cue execution, and real-time clock updates. State is maintained in component properties, allowing instant updates without page reloads.

### Real-Time Updates

The clock updates every second using Alpine.js intervals that call the `updateClock()` method on the Livewire component. This ensures the displayed time is always current. Cue status changes are immediately reflected in the interface through Livewire's reactive properties.

### Data Loading

The component uses eager loading to minimize database queries. Sessions are loaded with their client and producer relationships. Segments are loaded with client, producer, and tags. Cues are loaded with segment, cue type, operator, and tags. This approach ensures fast performance even with large numbers of cues.

### Filtering Logic

Filters are applied at the query level before data is retrieved from the database. This ensures only relevant data is loaded, improving performance. The Show Completed filter uses a whereIn clause to include or exclude completed and skipped cues. Segment and cue type filters use simple where clauses.

### Auto-Advance Algorithm

When a cue is executed or skipped, the auto-advance algorithm finds the next pending cue by querying cues in the current session where the time is greater than the current cue's time and status is pending. The results are ordered by time, and the first result is selected for standby. If no pending cues are found, standby is cleared.

## Keyboard Shortcuts (Future Enhancement)

Future versions will include keyboard shortcuts for common actions. Planned shortcuts include Space or Enter to execute the standby cue (GO), S to skip the standby cue, R to reset the selected cue, N to advance to the next pending cue, P to go to the previous cue, T to toggle between Table and Timeline views, and C to toggle Show Completed.

## Mobile Responsiveness

The interface is designed to work on tablets and large mobile devices, though desktop or laptop computers are recommended for show calling. The responsive design ensures all controls remain accessible on smaller screens. The table view automatically scrolls horizontally on narrow screens to maintain all columns.

## Troubleshooting

### Cues Not Appearing

If cues aren't visible, check that a session is selected using the session dropdown. Verify that filters aren't hiding the cues by clicking Reset Filters. Ensure the Show Completed checkbox is checked if you're looking for finished cues. Confirm that cues exist for the selected session by checking the Cues management interface.

### Auto-Advance Not Working

If auto-advance isn't selecting the next cue, verify that there are pending cues after the current one. Check that cue times are in the correct sequence. Ensure the next cue's status is pending, not standby or complete. Try manually selecting standby on the desired cue.

### Clock Not Updating

If the real-time clock isn't updating, refresh the page to restart the clock interval. Check that JavaScript is enabled in your browser. Verify that there are no browser console errors. The clock should update every second automatically.

### Status Changes Not Saving

If status changes aren't persisting, check your internet connection as Livewire requires connectivity. Verify that you have permission to modify cues. Check the browser console for errors. Try refreshing the page and attempting the action again.

## Future Enhancements

### Planned Features

Several enhancements are planned for future releases. **Keyboard shortcuts** will enable hands-free operation during shows. **Full-screen mode** will maximize screen real estate for the show calling interface. **Cue notes editing** will allow adding notes during the show for post-event review. **Content preview** will enable viewing media files directly from cues. **Multi-user sync** will allow multiple show callers to see real-time updates. **Cue timing adjustments** will permit changing cue times on the fly. **Segment reordering** will enable drag-and-drop reorganization. **Export to PDF** will create printable cue sheets.

### User Preferences

Future versions will include a preferences system allowing customization of font type, font size, highlight colors, and dark mode settings. These preferences will be stored per user and apply across the entire PLANNR application.

## Support and Training

### Getting Started

New users should start by creating a test event with a few sessions, segments, and cues. Practice using both view modes to understand their differences. Execute cues in a non-live environment to become familiar with the workflow. Use the auto-advance feature to experience the streamlined execution process.

### Training Resources

Comprehensive video tutorials will be available showing the complete show calling workflow. Documentation includes this guide plus quick reference cards for common operations. Live training sessions can be arranged for production teams. Support is available through the help system at https://help.manus.im.

## Conclusion

The Show Calling Interface provides professional-grade production control for live events. Its dual view modes, real-time updates, and streamlined execution workflow make it an essential tool for show callers and technical directors. Integration with segments, cues, content, and users creates a unified system that supports the entire production process from planning through execution.

---

**Last Updated**: October 31, 2025  
**Version**: 1.0  
**Module**: Show Calling Interface  
**Application**: PLANNR Event Control System
