import React, { useEffect, useState, useCallback } from 'react';
import { View, Text, Button, TextInput, StyleSheet, Alert } from 'react-native';
import axios from 'axios';
import { useFocusEffect } from '@react-navigation/native';

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
      <Text style={styles.title}>Trivia Details</Text>
      <Text>Question: {trivia.trivia_question}</Text>

      {trivia.trivia_answer ? (
  <Text>Answer: {trivia.trivia_answer}</Text>
) : (
  <>
    <TextInput
      placeholder="Enter your guess"
      value={guess}
      onChangeText={setGuess}
      style={styles.input}
    />
    <Button title="Submit Guess" onPress={handleGuess} />
  </>
)}


      <Text>Difficulty: {trivia.difficulty}</Text>
      <Text>Created By: {trivia.username}</Text>

      {loggedInUser === trivia.username && (
        <View style={styles.buttonRow}>
          <Button
            title="Update"
            onPress={() => navigation.navigate('UpdateTrivia', { id: trivia.id, loggedInUser })}
          />
          <Button
            title="Delete"
            color="red"
            onPress={() => {
              axios.delete(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${trivia.id}`)
                .then(() => navigation.navigate('Home'))
                .catch(err => console.error('Delete error:', err));
            }}
          />
        </View>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 16 },
  title: { fontSize: 24, marginBottom: 16 },
  input: { borderWidth: 1, padding: 8, marginBottom: 12, borderRadius: 4 },
  buttonRow: { flexDirection: 'row', justifyContent: 'space-around', marginTop: 20 }
});
