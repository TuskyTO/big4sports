import React, { useState } from 'react';
import { View, Text, TextInput, Button, StyleSheet, Alert } from 'react-native';
import axios from 'axios';
import { FontAwesome } from '@expo/vector-icons';

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
      <View style={styles.header}>
        <Text style={styles.headerText}>Big4Sports</Text>
        <FontAwesome name="trophy" size={28} color="#e6b800" style={styles.trophy} />
      </View>

      <TextInput
        style={styles.input}
        placeholder="Username"
        value={username}
        onChangeText={setUsername}
        autoCapitalize="none"
      />
      <TextInput
        style={styles.input}
        placeholder="Password"
        value={password}
        onChangeText={setPassword}
        secureTextEntry
      />
      <TextInput
        style={styles.input}
        placeholder="Confirm Password"
        value={confirm}
        onChangeText={setConfirm}
        secureTextEntry
      />

      <View style={styles.buttonContainer}>
        <Button title="Register" onPress={handleRegister} />
      </View>

      <View style={styles.buttonContainer}>
        <Button title="Login Instead" onPress={() => navigation.navigate('Login')} color="#1d3557" />
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f2f2f2',
    paddingHorizontal: 20,
    paddingTop: 50,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 30,
  },
  headerText: {
    fontSize: 28,
    fontWeight: 'bold',
    marginRight: 10,
    color: '#1c1c1c',
  },
  trophy: {
    marginTop: 2,
  },
  input: {
    backgroundColor: '#fff',
    padding: 12,
    marginBottom: 15,
    borderRadius: 8,
    borderColor: '#ccc',
    borderWidth: 1,
  },
  buttonContainer: {
    marginVertical: 8,
  },
});