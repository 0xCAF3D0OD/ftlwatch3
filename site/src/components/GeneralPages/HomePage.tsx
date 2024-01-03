// import AppLogo from '../../assets/logo_transparent_small.png';
import projectsLogo from '../../assets/projects.svg';
import usersStatsLogo from '../../assets/stats-users.svg';
import { Avatar, Button, Card, CardBody, CardFooter } from '@material-tailwind/react';
import { useLogin } from 'Hooks/LoginProvider';
import { commonTitle } from 'Utils/commonTitle';
import React, {useState} from 'react';
import {Link, useNavigate} from 'react-router-dom';
import '../../main.css'
// import { TableauPage } from 'Tableau/Tableau';

interface Props {
  path: string;
  name: string;
}
const GoToTableau = ({path, name}: Props) => {
  const navigate = useNavigate();

  const goToPage = () => {
    navigate(path);
  };

  return (
    <div className='data-options' onClick={goToPage}>
      {name}
    </div>
  )
};
export default function HomePage(): JSX.Element {
  const { isLogged, userInfos } = useLogin();

  React.useEffect(() => {document.title = commonTitle('Home');}, []);

  const options1 = [
    { img: projectsLogo, path : "/basics/projects", name: "projects"},
    { img: usersStatsLogo, path: "/projects/tinder", name: "tinder"},
    { img: usersStatsLogo, path: "/Tableau", name: "stats table"},
    { img: usersStatsLogo, path: "/basics/projects", name: "projects"},
  ]
  const options2 = [
    { img: usersStatsLogo, path: "/locations/peaks", name: "pres. peaks"},
    { img: usersStatsLogo, path: "/locations/userstotal", name: "users pres."},
    { img: usersStatsLogo, path: "/locations/love?graph=love_actual_2d", name: "love recent"},
    { img: usersStatsLogo, path: "/locations/love?graph=love_cursus_2d", name: "love cursus"},
  ]

  return (

    <div className='grid lg:grid-cols-4 h-screen pb-52 p-5 gap-16' >
      <div className='py-10 px-5 flex justify-center items-center row-span-2 lg:col-span-1 order-last lg:order-first'>
        <div className="w-full h-full max-w-[600px] min-w-[200px] shadow-none">
          <div className='main-card-options'>
            <div className='x4-card-options'>
            {options1.map((option) =>(
              <div className="card-options">
                <img src={option.img} className='image-options'></img>
                <GoToTableau path={option.path} name={option.name} />
              </div>
            ))}
            </div>
            {/*<div className='x4-card-options'>*/}
            {/*  {options2.map((option) => (*/}
            {/*    <div className="card-options">*/}
            {/*      <img src={projectsLogo} className='image-options'></img>*/}
            {/*      <GoToTableau path={option.path} name={option.name}/>*/}
            {/*    </div>*/}
            {/*  ))}*/}
            {/*</div>*/}
          </div>
        </div>
      </div>

      <div className='flex justify-center items-center row-span-2 h-full lg:col-span-3'>
        { userInfos?.citation && userInfos?.citation.length > 0 &&

          <Card className="big-card mx-2 mt-6 !mb-0 max-w-[600px] shadow-none">
            <CardBody className='py-2 flex flex-row justify-center gap-6 items-center'>
              { userInfos?.citation_avatar && userInfos?.citation_avatar.length > 0 &&
                <Avatar src={userInfos?.citation_avatar} alt="citator" />
              }
              <p className='text-center text-xl text-black dark:text-white tracking-wide font-extrabold'>
                {userInfos.citation}
              </p>
            </CardBody>
          </Card>
        }
        <div className='h-full grid grid-rows-2'>
          <div className='h-full flex items-center col-span-1'>
            <p>
              <div className='gap-5 p-5'>
                {/*<img src={AppLogo}*/}
                {/*     className="h-12 flip-horizontal"*/}
                {/*/>*/}
                <h1 className='text-white tracking-wide font-extrabold'>
                  Welcome to 42lwatch _V3_
                </h1>
              </div>
              <div>
                <p className="text-white leading-relaxed px-10 font-bold text-2xl max-w-2xl">
                  Take a dive into a one-stop solution for accessing real-time data on all 42Lausanne students.
                </p>
              </div>
            </p>
          </div>
          <div className="col-span-1 flex items-start">
            <p>
              <div className='flex items-center gap-5 p-5'>
                <h1 className='text-white tracking-wide font-extrabold'>
                  Open-source
                </h1>
              </div>
              <div>
                <p className="text-white leading-relaxed px-10 my-2 font-bold text-2xl max-w-3xl">
                  <p className="pb-5 text-white ">
                    Explore detailed student profiles in a user-friendly interface and join us in making this tool even better.
                  </p>
                  Open-source at its core, we invite you to contribute to our ever-evolving codebase.
                  <p className='pt-5 text-white '>
                    Let's transform 42Lausanne's student data experience together!
                  </p>
                </p>
                {!isLogged &&
                  <p>
                    To access most of the pages, you need to be <a href="/api/?page=login&action=authorizeapi">logged</a> first
                  </p>
                }
              </div>
            </p>
          </div>

        {/*<Card className="big-card mx-2 mt-6 max-w-[600px] shadow-none">*/}
        {/*  <CardBody className=''>*/}
        {/*    <div className='flex flex-row justify-center items-center gap-5 p-5'>*/}
        {/*      <img src={AppLogo}*/}
        {/*        className="h-12 flip-horizontal"*/}
        {/*      />*/}
        {/*      <p className='text-center text-3xl text-black dark:text-white tracking-wide font-extrabold'>*/}
        {/*      Welcome to 42lwatch (V3) !*/}
        {/*      </p>*/}
        {/*      <img src={AppLogo}*/}
        {/*        className="h-12"*/}
        {/*      />*/}
        {/*    </div>*/}

        {/*    <p className="my-2">*/}
        {/*    The (best) website for everything related to 42Lausanne (student side)*/}
        {/*    </p>*/}

        {/*    {!isLogged &&*/}
        {/*    <p>*/}
        {/*      To access most of the pages, you need to be <a href="/api/?page=login&action=authorizeapi">logged</a> first*/}
        {/*    </p>*/}
        {/*    }*/}
        {/*    <p className="my-2">*/}
        {/*    The code is open source, so feel free to leave a pull request on the project if you see any upgrade you want to see*/}
        {/*    </p>*/}
        {/*  </CardBody>*/}

        {/*  <CardFooter className="pt-0 flex gap-2">*/}
        {/*    {*/}
        {/*      !isLogged &&*/}
        {/*  <a href={'/api/?page=login&action=authorizeapi'}>*/}
        {/*    <Button color='blue'>Sign in</Button>*/}
        {/*  </a>*/}
        {/*    }*/}

        {/*    <Link to={'https://github.com/Jerome-JJT/ftlwatch3'}>*/}
        {/*      <Button color='deep-purple'>Github</Button>*/}
        {/*    </Link>*/}
        {/*  </CardFooter>*/}
        {/*</Card>*/}
        </div>
      </div>
    </div>
  );
}
