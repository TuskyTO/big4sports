import React, { useState } from 'react';
import { View, Text, TextInput, Button, StyleSheet, Alert } from 'react-native';
import axios from 'axios';

export default function LoginScreen({ navigation }) {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);

  const handleLogin = () => {
    if (!username || !password) {
      Alert.alert('Error', 'Please enter both username and password.');
      return;
    }
    setLoading(true);
    axios.post('http://10.0.2.2/big4sports/backend/api_users.php', {
      action: 'login',
      username,
      password
    })
    .then(response => {
      setLoading(false);
      if (response.data.success) {
        // Store user data as needed (e.g., in Context or AsyncStorage)
        // Navigate to HomeScreen upon successful login
        navigation.navigate('Home', { username });
      } else {
        Alert.alert('Login Failed', response.data.error || 'Invalid credentials');
      }
    })
    .catch(error => {
      setLoading(false);
      console.error(error);
      Alert.alert('Login Error', error.message);
    });
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Login</Text>
      <TextInput
        placeholder="Username"
        value={username}
        style={styles.input}
        onChangeText={setUsername}
      />
      <TextInput
        placeholder="Password"
        value={password}
        style={styles.input}
        secureTextEntry
        onChangeText={setPassword}
      />
      <Button title={loading ? 'Logging in...' : 'Login'} onPress={handleLogin} />
      <Text style={styles.link} onPress={() => navigation.navigate('Register')}>
        Don't have an account? Register here.
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
    container: { padding: 16, flex: 1, justifyContent: 'center' },
    title: { fontSize: 24, marginBottom: 16, textAlign: 'center' },
  });
  
