const express = require('express');
const bodyParser = require('body-parser');
const jwt = require('jsonwebtoken');
const paypal = require('@paypal/checkout-server-sdk');
const path = require('path');

const app = express();
const PORT = 3000;
const users = {};
const secret = 'supersecret'; // JWT secret

// PayPal setup
const Environment = paypal.core.LiveEnvironment; 
const paypalClient = new paypal.core.PayPalHttpClient(
  new Environment(
    'AcKNQugfH6LQ7ApKcATgXGjvRcnZgCwg17pJeHVI4Iu2RXLzDrl5c7q22zYY-sVtr417SPImLGCjnqYN', 
    'EAbCP8nqoRoS6YDEF2yXUwG_qS9_jG23_5j1RJbpWjZjzIcTP8x1nXzNUjIN2HwyPNaNMDZ0B7mIRaBm' 
  )
);

app.use(bodyParser.json());
app.use(express.static('public'));

// Middleware to verify token
function authenticate(req, res, next) {
  const token = req.headers.authorization?.split(' ')[1];
  if (!token) return res.status(401).json({ message: 'Unauthorized' });
  try {
    const user = jwt.verify(token, secret);
    req.user = user;
    next();
  } catch {
    res.status(403).json({ message: 'Invalid token' });
  }
}

app.post('/signup', (req, res) => {
  const { email, password } = req.body;
  if (users[email]) return res.json({ message: 'Already registered' });
  users[email] = { password, balance: 0, lastSurvey: 0 };
  res.json({ message: 'Signup successful' });
});

app.post('/login', (req, res) => {
  const { email, password } = req.body;
  if (!users[email] || users[email].password !== password) return res.json({ message: 'Invalid credentials' });
  const token = jwt.sign({ email }, secret);
  res.json({ token });
});

app.get('/balance', authenticate, (req, res) => {
  res.json({ balance: users[req.user.email].balance });
});

app.get('/survey', authenticate, (req, res) => {
  const user = users[req.user.email];
  const now = Date.now();
  if (now - user.lastSurvey < 3600000) {
    return res.json({ message: 'Come back in 1 hour!' });
  }
  user.balance += 10;
  user.lastSurvey = now;
  res.json({ questions: [
    'What is your name?', 'What country are you in?', 'Your birth date?', 'Do you use PayPal?',
    'How much money do you make monthly?', 'What device do you use?', 'Do you play games?', 'Which bank do you use?',
    'Would you use TechCoin again?', 'Rate this platform out of 10?'
  ]});
});

app.post('/withdraw', authenticate, async (req, res) => {
  const user = users[req.user.email];
  if (user.balance < 10) return res.json({ message: 'Not enough TechCoins' });

  user.balance -= 10;

  // Placeholder PayPal payout
  const request = new paypal.payouts.PayoutsPostRequest();
  request.requestBody({
    sender_batch_header: {
      sender_batch_id: 'batch_' + Date.now(),
      email_subject: 'You have a TechCoin payment!'
    },
    items: [{
      recipient_type: 'EMAIL',
      amount: { value: '1.00', currency: 'USD' },
      receiver: req.user.email,
      note: 'Thanks for using TechCoin!',
      sender_item_id: 'item_1'
    }]
  });

  try {
    const response = await paypalClient.execute(request);
    res.json({ message: 'Withdrawal processed!' });
  } catch (e) {
    res.json({ message: 'Error with PayPal payout' });
  }
});

app.listen(PORT, () => console.log(`Server running on http://localhost:${PORT}`));
