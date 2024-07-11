import React, { useState, useEffect } from 'react';
import axios from 'axios';

function App() {
  const [signups, setSignups] = useState([]);

  useEffect(() => {
    fetchSignups();
  }, []);

  const fetchSignups = async () => {
    try {
      const response = await axios.post(taskAdminData.ajaxurl, new URLSearchParams({
        action: 'get_signups',
        nonce: taskAdminData.nonce
      }));
      console.log('AJAX response:', response.data);
      setSignups(response.data.data.signups);
    } catch (error) {
      console.error('Error fetching signups:', error);
      setError('Failed to fetch signups');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div class="task-signups-container">
      <h1 class="task-signups-title">Task Signups</h1>
      <table class="task-signups-table">
        <thead>
          <tr>
            <th class="task-signups-th">Name</th>
            <th class="task-signups-th">Email</th>
            <th class="task-signups-th">Phone</th>
            <th class="task-signups-th">Address</th>
            <th class="task-signups-th">Hobbies</th>
            <th class="task-signups-th">Signup Date</th>
          </tr>
        </thead>
        <tbody>
          {signups.map((signup) => (
            <tr key={signup.id} class="task-signups-tr">
              <td class="task-signups-td" data-label="Name">{signup.name}</td>
              <td class="task-signups-td" data-label="Email">{signup.email}</td>
              <td class="task-signups-td" data-label="Phone">{signup.phone}</td>
              <td class="task-signups-td" data-label="Address">{signup.address}</td>
              <td class="task-signups-td" data-label="Hobbies">{signup.hobbies}</td>
              <td class="task-signups-td" data-label="Signup Date">{signup.signup_date}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
);
}

export default App;