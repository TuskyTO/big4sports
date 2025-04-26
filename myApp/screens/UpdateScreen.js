import React, { useEffect, useState } from 'react';
import { View, Text, TextInput, Button, StyleSheet } from 'react-native';
import axios from 'axios';
import { FontAwesome } from '@expo/vector-icons';

export default function UpdateScreen({ route, navigation }) {
  const { id, loggedInUser } = route.params;  // The ID passed from View or a list screen

  const [question, setQuestion] = useState('');
  const [answer, setAnswer] = useState('');
  const [difficulty, setDifficulty] = useState('1');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    // 1. Fetch existing trivia item to populate fields
    axios
      .get(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${id}&username=${loggedInUser}`)
      .then((response) => {
        if (response.data) {
          setQuestion(response.data.trivia_question);
          setAnswer(response.data.trivia_answer);
          setDifficulty(String(response.data.difficulty));
        }
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [id]);

  const handleUpdate = () => {
    // 2. Send PUT request with updated fields
    axios
      .put(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${id}`, {
        trivia_question: question,
        trivia_answer: answer,
        difficulty: parseInt(difficulty, 10),
      })
      .then((res) => {
        console.log('Update response:', res.data);
        navigation.goBack(); // or navigate to wherever you want
      })
      .catch((err) => setError(err.message));
  };

  if (loading) {
    return (
      <View style={styles.container}>
        <Text>Loading existing data...</Text>
      </View>
    );
  }
  if (error) {
    return (
      <View style={styles.container}>
        <Text>Error: {error}</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.headerText}>Big4Sports</Text>
        <FontAwesome name="trophy" size={28} color="#e6b800" style={styles.trophy} />
      </View>

      <TextInput
        style={styles.input}
        placeholder="Trivia Question"
        value={question}
        onChangeText={setQuestion}
      />
      <TextInput
        style={styles.input}
        placeholder="Trivia Answer"
        value={answer}
        onChangeText={setAnswer}
      />
      <TextInput
        style={styles.input}
        placeholder="Difficulty (1-10)"
        value={difficulty}
        onChangeText={setDifficulty}
        keyboardType="numeric"
      />

      <View style={styles.buttonContainer}>
      <Button title="Update Trivia" onPress={handleUpdate} />
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
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
  input: {
    backgroundColor: '#fff',
    padding: 12,
    marginBottom: 15,
    borderRadius: 8,
    borderColor: '#ccc',
    borderWidth: 1,
  },
  buttonContainer: {
    marginTop: 15,
  },
});
