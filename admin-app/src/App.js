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
    <div>
      <h1>Task Signups</h1>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Hobbies</th>
            <th>Signup Date</th>
          </tr>
        </thead>
        <tbody>
          {signups.map((signup) => (
            <tr key={signup.id}>
              <td>{signup.name}</td>
              <td>{signup.email}</td>
              <td>{signup.phone}</td>
              <td>{signup.address}</td>
              <td>{signup.hobbies}</td>
              <td>{signup.signup_date}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default App;