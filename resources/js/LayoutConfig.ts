import {
  ArrowLeftStartOnRectangleIcon,
  CogIcon,
  UserCircleIcon,
  TableCellsIcon,
  SpeakerWaveIcon,
  ChatBubbleLeftIcon,
} from '@heroicons/vue/24/outline';
import { FunctionalComponent } from 'vue';

interface NavigationItem {
  name: string;
  route: string;
  icon: FunctionalComponent;
}

const layoutNavigationItems: NavigationItem[] = [
  { name: 'DataTable Example', route: 'page1', icon: TableCellsIcon },
  { name: 'Reverb Example', route: 'page2', icon: SpeakerWaveIcon },
  { name: 'Job Notifications', route: 'jobs', icon: ChatBubbleLeftIcon },
];

const userNavigationItems: NavigationItem[] = [
  { name: 'Profile', route: 'me.profile', icon: UserCircleIcon },
  { name: 'Settings', route: 'me.settings', icon: CogIcon },
  { name: 'Sign out', route: 'logout', icon: ArrowLeftStartOnRectangleIcon },
];

const appName: string = 'Test';
const appLogo: string = 'https://tailwindui.com/img/logos/mark.svg?color=white';

export {
  layoutNavigationItems,
  userNavigationItems,
  NavigationItem,
  appName,
  appLogo,
};
