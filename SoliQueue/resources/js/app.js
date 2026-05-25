import './bootstrap';
import 'preline';
import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

// Import Alpine.js components
import sessionsManager from './components/admin/sessionsManager';
import affectationsManager from './components/admin/affectationsManager';
import candidatsManager from './components/admin/candidatsManager';
import formateursManager from './components/admin/formateursManager';
import dashboardManager from './components/formateur/dashboardManager';
import ticketManager from './components/candidat/ticketManager';

// Register components on window
window.Alpine = Alpine;
window.Sortable = Sortable;
window.sessionsManager = sessionsManager;
window.affectationsManager = affectationsManager;
window.candidatManager = candidatsManager;
window.formateurManager = formateursManager;
window.dashboardManager = dashboardManager;
window.ticketManager = ticketManager;

Alpine.start();
