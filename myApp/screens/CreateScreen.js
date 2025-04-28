import React, { useState } from 'react';
import { View, Text, TextInput, Button, StyleSheet, Alert } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import axios from 'axios';

export default function CreateScreen({ navigation, route }) {
  const { loggedInUser } = route.params || {};
  const [question, setQuestion] = useState('');
  const [answer, setAnswer] = useState('');
  const [difficulty, setDifficulty] = useState('');

  const handleCreate = () => {
    if (!question || !answer || !difficulty) {
      Alert.alert('Error', 'Please fill in all fields.');
      return;
    }

    axios.post('http://10.0.2.2/big4sports/backend/api_trivia.php', {
      action: 'create_trivia',
      username: loggedInUser,
      trivia_question: question,
      trivia_answer: answer,
      difficulty: parseInt(difficulty, 10),
    })
    .then((response) => {
      console.log('Trivia Created:', response.data);
      Alert.alert('Success', 'Trivia Created!');
      navigation.goBack();
    })
    .catch(err => {
      console.error('Create Error:', err.response?.data || err.message);
      Alert.alert('Error', 'Failed to create trivia.');
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
        <Button title="Create Trivia" onPress={handleCreate} />
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
