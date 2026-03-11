// import Sidebar from "../components/Sidebar";
// import { Outlet } from "react-router-dom";
// import Header from "../components/Header";
// export default function AdminLayout() {
//   return (
//     <div className="admin-layout">
//       <Sidebar />

//       <div className="main">
//         <Header />

//         <div className="content">
//           <Outlet />
//         </div>
//       </div>
//     </div>
//   );
// }

import { useEffect, useState } from "react";
import { Outlet } from "react-router-dom";

import Sidebar from "../components/Sidebar";
import Header from "../components/Header";

export default function AdminLayout() {
  const [modules, setModules] = useState([]);

  const loadModules = () => {
    fetch("/api/admin/component.php?act=list")
      .then((res) => res.json())
      .then((result) => {
        if (result.status) {
          setModules(result.data.filter((i) => i.active == 1));
        }
      });
  };

  useEffect(() => {
    loadModules();
  }, []);

  return (
    <div className="admin-layout">
      <Sidebar modules={modules} />

      <div className="main">
        <Header />

        <div className="content">
          <Outlet context={{ loadModules }} />
        </div>
      </div>
    </div>
  );
}
