import React, { useEffect, useState, useCallback } from 'react';
import { View, Text, Button, TextInput, StyleSheet, Alert } from 'react-native';
import axios from 'axios';
import { useFocusEffect } from '@react-navigation/native';
import { FontAwesome } from '@expo/vector-icons';

export default function ReadScreen({ route, navigation }) {
  const { id, loggedInUser } = route.params;
  const [trivia, setTrivia] = useState(null);
  const [error, setError] = useState('');
  const [guess, setGuess] = useState('');

  // Fetch trivia when screen is focused
  useFocusEffect(
    useCallback(() => {
      axios.get(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${id}&username=${loggedInUser}`)
        .then(response => setTrivia(response.data))
        .catch(err => setError(err.message));
    }, [id, loggedInUser])
  );

  const handleGuess = () => {
    axios.post('http://10.0.2.2/big4sports/backend/api_trivia.php', {
      id: trivia.id,
      guess: guess,
      username: loggedInUser,
      action: 'guess'
    }, {
      headers: {
        'Content-Type': 'application/json',
      }
    })
    .then((response) => {
      alert(response.data.message);
      if (response.data.success) {
        // Refresh trivia to reveal the answer
        axios.get(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${id}&username=${loggedInUser}`)
          .then(res => setTrivia(res.data))
          .catch(err => console.error("Failed to refresh after guess:", err));
      }
    })
    .catch((error) => {
      console.error('Guess error:', error);
    });
  };

  if (error) {
    return (
      <View style={styles.container}>
        <Text>Error: {error}</Text>
      </View>
    );
  }

  if (!trivia || !trivia.trivia_question) {
    return (
      <View style={styles.container}>
        <Text>Loading...</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.headerText}>Big4Sports</Text>
        <FontAwesome name="trophy" size={28} color="#e6b800" style={styles.trophy} />
      </View>

      <Text style={styles.label}>Question:</Text>
      <Text style={styles.text}>{trivia.trivia_question}</Text>

      <Text style={styles.label}>Difficulty:</Text>
      <Text style={styles.text}>{trivia.difficulty}</Text>

      <Text style={styles.label}>Created By:</Text>
      <Text style={styles.text}>{trivia.username}</Text>

      {trivia.trivia_answer ? (
  <>
    <Text style={styles.label}>Answer:</Text>
    <Text style={styles.answer}>{trivia.trivia_answer}</Text>
  </>
) : (

        <>
          <TextInput
            style={styles.input}
            placeholder="Enter your guess"
            value={guess}
            onChangeText={setGuess}
          />
          <View style={styles.buttonContainer}>
            <Button title="Submit Guess" onPress={handleGuess} />
          </View>
        </>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  answer: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 10,
    color: '#1d3557',
  },  
  container: {
    flex: 1,
    backgroundColor: '#f2f2f2',
    paddingHorizontal: 20,
    paddingTop: 30,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 25,
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
  label: {
    fontSize: 16,
    fontWeight: 'bold',
    marginTop: 10,
    color: '#1c1c1c',
  },
  text: {
    fontSize: 16,
    marginBottom: 10,
  },
  input: {
    backgroundColor: '#fff',
    padding: 12,
    marginTop: 10,
    borderRadius: 8,
    borderColor: '#ccc',
    borderWidth: 1,
  },
  buttonContainer: {
    marginTop: 15,
  },
});

