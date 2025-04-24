import React, { useEffect, useState } from 'react';
import { View, Text, Button, TextInput, StyleSheet, Alert } from 'react-native';
import axios from 'axios';

export default function ReadScreen({ route, navigation }) {
  const { id } = route.params;
  const { loggedInUser } = route.params;  // Access loggedInUser from route.params
  const [trivia, setTrivia] = useState(null);
  const [error, setError] = useState('');
  const [guess, setGuess] = useState('');  // State to store user's guess

  useEffect(() => {
    // Fetch trivia question by ID
    axios.get(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${id}`)
      .then(response => setTrivia(response.data))
      .catch(err => setError(err.message));  // Set error if the request fails
  }, [id]);

  const handleGuess = () => {
    // Log the data being sent for debugging
    console.log("Sending data:", { id: trivia.id, guess: guess, username: loggedInUser });
  
    axios.post('http://10.0.2.2/big4sports/backend/api_trivia.php', {
      id: trivia.id,
      guess: guess,
      username: loggedInUser,
      action: 'guess'  // Ensure action is provided
    }, {
      headers: {
        'Content-Type': 'application/json',  // Ensure correct content type
      }
    })
    .then((response) => {
      if (response.data.success) {
        alert(response.data.message);
        setTrivia(prev => ({ ...prev, is_answer_revealed: 1 }));  // Reveal the answer if correct
      } else {
        alert(response.data.message);
      }
    })
    .catch((error) => {
      console.error('Error guessing answer:', error);
    });
  };  

  // Error or loading states
  if (error) {
    return (
      <View style={styles.container}>
        <Text>Error: {error}</Text>
      </View>
    );
  }

  if (!trivia) {
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

      {/* Show answer if revealed, else show input for guess */}
      {trivia.is_answer_revealed === 1 ? (
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

      {/* Show Update/Delete buttons only if loggedInUser is the creator */}
      {loggedInUser === trivia.username && (
        <View style={styles.buttonRow}>
          <Button
            title="Update"
            onPress={() => navigation.navigate('UpdateTrivia', { id: trivia.id })}
          />
          <Button
            title="Delete"
            color="red"
            onPress={() => {
              axios.delete(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${trivia.id}`)
                .then(response => {
                  navigation.navigate('Home');
                })
                .catch(error => {
                  console.error(error);
                });
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
