import React, { useState } from 'react';
import { View, Text, TextInput, Button, StyleSheet, Alert } from 'react-native';
import axios from 'axios';

export default function RegisterScreen({ navigation }) {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [loading, setLoading] = useState(false);

  const handleRegister = () => {
    if (!username || !password || !confirm) {
      Alert.alert('Error', 'All fields are required.');
      return;
    }
    if (password !== confirm) {
      Alert.alert('Error', 'Passwords do not match.');
      return;
    }
    if (password.length < 10) {
      Alert.alert('Error', 'Password must be at least 10 characters long.');
      return;
    }
    setLoading(true);
    axios.post('http://10.0.2.2/big4sports/backend/api_trivia.php', {
      action: 'register',
      username,
      password
    })
    .then(response => {
      setLoading(false);
      if (response.data.success) {
        Alert.alert('Success', 'Registration successful. Please log in.');
        navigation.navigate('Login');
      } else {
        Alert.alert('Registration Failed', response.data.error || 'Registration error');
      }
    })
    .catch(error => {
      setLoading(false);
      console.error(error);
      Alert.alert('Error', error.message);
    });
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Register</Text>
      <TextInput
        placeholder="Username"
        value={username}
        style={styles.input}
        onChangeText={setUsername}
      />
      <TextInput
        placeholder="Password (min 10 characters)"
        value={password}
        style={styles.input}
        secureTextEntry
        onChangeText={setPassword}
      />
      <TextInput
        placeholder="Confirm Password"
        value={confirm}
        style={styles.input}
        secureTextEntry
        onChangeText={setConfirm}
      />
      <Button title={loading ? 'Registering...' : 'Register'} onPress={handleRegister} />
      <Text style={styles.link} onPress={() => navigation.navigate('Login')}>
        Already have an account? Login here.
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { padding: 16, flex: 1, justifyContent: 'center' },
  title: { fontSize: 24, marginBottom: 16, textAlign: 'center' },
  input: { borderWidth: 1, borderColor: '#ccc', padding: 10, marginBottom: 12, borderRadius: 4 },
  link: { color: 'blue', marginTop: 12, textAlign: 'center' }
});
