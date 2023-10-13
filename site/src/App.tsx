import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';

import './App.css';

import HomePage from './components/Home/HomePage';

import NavBar from './components/NavBar/NavBar';

import LoginApi from './components/Log/LoginApi';
import Logout from './components/Log/Logout';

import CreateAccountPage from './components/Log/CreateAccountPage';
import LeftDrawer from './components/NavBar/LeftDrawer';
import axios from 'axios';
import { AxiosErrorText } from './components/Hooks/AxiosErrorText';

import { LogPage } from './components/Log/LogPage';

import { NotifDisplay } from './components/Notifications/NotifDisplay';
import { UserGroupsPage } from './components/AdminPages/UserGroups';
import { GroupPermissionsPage } from './components/AdminPages/GroupPermissions';
import { PagePermissionsPage } from './components/AdminPages/PagePermissions';
import { useLogin } from './components/Hooks/LoginProvider';
import TestPage from './components/Test/Test';
import { TableauPage } from 'Tableau/Tableau';
import { ImagePage } from 'Image/Image';
import { PoolfilterVisibilityPage } from 'AdminPages/PoolfilterVisibility';
import { UsersVisibilityPage } from 'AdminPages/UsersVisibility';
import { CoalitionsPage } from 'BasicPages/CoalitionsPage';
import { CampusPage } from 'BasicPages/CampusPage';
import { TitlesPage } from 'BasicPages/TitlesPage';
import { ProductsPage } from 'BasicPages/ProductPage';
import { CursusPage } from 'BasicPages/CursusPage';
import { GroupsPage } from 'BasicPages/GroupsPage';
import { AchievementsPage } from 'BasicPages/AchievementsPage';
// import Place from './components/Place/Place';

export default function App(): JSX.Element {
  const { isLogged, getUserData, logout } = useLogin();
  const [openedMenu, setOpenedMenu] = React.useState('');

  const logging = import.meta.env.DEV;

  React.useEffect(() => {
    if (!location.pathname.includes('loginapi')) {
      getUserData();
    }
  }, [getUserData]);

  axios.interceptors.request.use(
    (req) => {
      req.baseURL = '/api';
      // req.meta.requestStartedAt = new Date().getTime();
      return req;
    });

  axios.interceptors.response.use(
    (response) => {
      if (logging && response.config.method !== 'OPTIONS') { console.log('inter res', response); }

      return response;
    },
    (error) => {
      if (logging && error.response.config.method !== 'OPTIONS') { console.log('myaxiosintercept', AxiosErrorText(error), error); }

      if (error.response && error.response.status === 401) {
        logout();
      }
      // if ((error.response && error.response.data && error.response.data.detail) === 'Invalid token.' && error.request.responseURL.indexOf("logout") === -1) {
      // }

      return Promise.reject(error);
    }
  );

  return (
    <Router>
      <NavBar
        openedMenu={openedMenu}
        setOpenedMenu={setOpenedMenu}
      />

      <LeftDrawer
        openedMenu={openedMenu}
        setOpenedMenu={setOpenedMenu}
      />

      <div className="grow bg-gray-100 dark:bg-gray-400">
        <Routes>
          <Route path="/" element={<HomePage />} />

          {!isLogged && (
            <>
              <Route path="/start" element={<p></p>} />
            </>
          )}
          {isLogged && (
            <>
            </>
          )}

          <Route path="/achievements" element={<AchievementsPage />} />
          <Route path="/campus" element={<CampusPage />} />
          <Route path="/coalitions" element={<CoalitionsPage />} />
          <Route path="/cursus" element={<CursusPage />} />
          <Route path="/groups" element={<GroupsPage />} />
          <Route path="/products" element={<ProductsPage />} />
          <Route path="/titles" element={<TitlesPage />} />


          <Route path="/tableau" element={<TableauPage />} />
          <Route path="/image" element={<ImagePage />} />

          <Route path="/login_users" element={<UsersVisibilityPage />} />
          <Route path="/login_groups" element={<UserGroupsPage />} />
          <Route path="/permissions" element={<GroupPermissionsPage />} />
          <Route path="/pages" element={<PagePermissionsPage />} />
          <Route path="/poolfilters" element={<PoolfilterVisibilityPage />} />

          <Route path="/test" element={<TestPage />} />

          {/* <Route path="/test" element={<Place loginer={loginer} />} /> */}

          <Route path="/login" element={<LogPage />} />
          <Route path="/loginapi" element={<LoginApi />} />
          <Route path="/logout" element={<Logout />} />
          <Route path="/createaccount" element={<CreateAccountPage />} />
        </Routes>
      </div>
      <NotifDisplay/>
    </Router>
  );
}
