const express = require('express');
const fs = require('fs');
const cors = require('cors');
const bodyParser = require('body-parser');
const app = express();

const USERS_FILE = 'users.json';
app.use(cors());
app.use(bodyParser.json());
app.use(express.static('.'));

function readUsers() {
  return JSON.parse(fs.existsSync(USERS_FILE) ? fs.readFileSync(USERS_FILE) : '[]');
}

function writeUsers(users) {
  fs.writeFileSync(USERS_FILE, JSON.stringify(users, null, 2));
}

app.post('/register', (req, res) => {
  const { email, phone, password } = req.body;
  const users = readUsers();

  if (users.find(u => u.email === email)) {
    return res.status(400).json({ message: 'User already exists' });
  }

  users.push({ email, phone, password, techcoins: 0, lastSurvey: 0 });
  writeUsers(users);
  res.json({ message: 'Registered successfully' });
});

app.post('/login', (req, res) => {
  const { email, password } = req.body;
  const users = readUsers();
  const user = users.find(u => u.email === email && u.password === password);

  if (!user) return res.json({ message: 'Invalid login' });
  res.json({ user });
});

app.post('/survey', (req, res) => {
  const { email } = req.body;
  const users = readUsers();
  const user = users.find(u => u.email === email);
  const now = Date.now();

  if (!user) return res.json({ message: 'User not found' });

  if (now - user.lastSurvey < 3600000) {
    return res.json({ message: 'Please wait 1 hour between surveys' });
  }

  user.techcoins += 20 * 10; // 20 questions x 10 techcoins
  user.lastSurvey = now;
  writeUsers(users);
  res.json({ message: 'Survey submitted!', techcoins: user.techcoins });
});

app.post('/withdraw', (req, res) => {
  const { email } = req.body;
  const users = readUsers();
  const user = users.find(u => u.email === email);

  if (!user || user.techcoins < 1) {
    return res.json({ message: 'Not enough TechCoins' });
  }

  // Fake withdrawal
  res.json({ message: `Withdrawal request received for ${user.techcoins} TechCoins` });
  user.techcoins = 0;
  writeUsers(users);
});

app.listen(3000, () => console.log('Server running on http://localhost:3000'));
